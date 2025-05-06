<?php

namespace App\Services;

use App\Helpers\MessageHelper;
use App\Jobs\ProcessNotificationJob;
use App\Models\Integration;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Integrations\MercadoPagoIntegration;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
	protected $transactionService;
	protected $mercadoPagoIntegration;

	public function __construct(TransactionService $transactionService, MercadoPagoIntegration $mercadoPagoIntegration)
	{
		$this->transactionService = $transactionService;
		$this->mercadoPagoIntegration = $mercadoPagoIntegration;
	}

	public function getAllInvoices($filters = [])
	{
		$query = Invoice::query();

		if (!empty($filters['name'])) {
			$query->whereHas('user', function ($subQuery) use ($filters) {
				$subQuery->where('name', 'LIKE', '%' . $filters['name'] . '%');
			});
		}

		if (!empty($filters['invoice_id'])) {
			$query->where('id', $filters['invoice_id']);
		}

		if (!empty($filters['status'])) {
			$query->where('status', $filters['status']);
		}

		if (!empty($filters['date_range'])) {
			$datas = explode(' até ', $filters['date_range']);

			if (count($datas) > 1) {
				$filters['start_date'] = Carbon::createFromFormat('d/m/Y', $datas[0])->format('Y-m-d');
				$filters['end_date'] = Carbon::createFromFormat('d/m/Y', $datas[1])->format('Y-m-d');

				if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
					$query->whereBetween('due_at', [$filters['start_date'], $filters['end_date']]);
				}
			} else {
				$dateSearch = Carbon::createFromFormat('d/m/Y', $filters['date_range'])->format('Y-m-d');
				$query->where('due_at', $dateSearch);
			}
		}

		// Faturas filtradas
		$perPage = $filters['per_page'] ?? 10;
		$invoices = $query->orderBy('id', 'DESC')->paginate($perPage);

		// Cálculo dos valores SOMENTE da página atual
		$totalAmount = $invoices->sum('total');
		$paidAmount = $invoices->where('status', 24)->sum('total');
		$unpaidAmount = $invoices->whereIn('status', [23, 25, 28, 29])->sum('total');

		// Contagem das faturas SOMENTE da página atual
		$totalInvoices = $invoices->count();
		$paidInvoices = $invoices->where('status', 24)->count();
		$unpaidInvoices = $invoices->whereIn('status', [23, 25, 28, 29])->count();

		return compact('invoices', 'totalAmount', 'paidAmount', 'unpaidAmount', 'totalInvoices', 'paidInvoices', 'unpaidInvoices');
	}

	public function getInvoiceById($id)
	{
		return Invoice::findOrFail($id);
	}

	public function createInvoice($data)
	{
		DB::beginTransaction(); // Inicia transação para garantir consistência

		try {
			// Criar fatura
			$invoice = Invoice::create([
				'user_id' => $data['user_id'],
				'integration_id' => $data['integration_id'],
				'method_type' => $data['method_type'],
				'total' => collect($data['items'])->sum(fn($item) => $item['price_total']),
				'status' => $data['status'],
				'due_at' => Carbon::createFromFormat('d/m/Y', $data['due_at'])->format('Y-m-d'),
			]);

			// Criar itens da fatura
			foreach ($data['items'] as $item) {
				InvoiceItem::create([
					'invoice_id' => $invoice->id,
					'description' => $item['description'],
					'quantity' => $item['quantity'],
					'price_unit' => $item['price_unit'],
					'price_total' => $item['price_total'],
				]);
			}

			DB::commit(); // Confirma a transação
			return $invoice;
		} catch (Exception $e) {
			DB::rollBack(); // Reverte se houver erro
			throw new Exception("Erro ao criar fatura: " . $e->getMessage());
		}
	}

	public function updateInvoice($id, $data)
	{
		DB::beginTransaction();

		try {
			// Buscar fatura
			$invoice = Invoice::findOrFail($id);

			// Atualizar fatura
			$invoice->update([
				'user_id' => $data['user_id'] ?? $invoice->user_id,
				'integration_id' => $data['integration_id'],
				'method_type' => $data['method_type'],
				'total' => collect($data['items'])->sum(fn($item) => $item['price_total']),
				'status' => $data['status'],
				'due_at' => Carbon::createFromFormat('d/m/Y', $data['due_at'])->format('Y-m-d'),
			]);

			// Remover itens antigos
			InvoiceItem::where('invoice_id', $id)->delete();

			// Adicionar novos itens
			foreach ($data['items'] as $item) {
				InvoiceItem::create([
					'invoice_id' => $invoice->id,
					'description' => $item['description'],
					'quantity' => $item['quantity'],
					'price_unit' => $item['price_unit'],
					'price_total' => $item['price_total'],
				]);
			}

			DB::commit();
			return $invoice;
		} catch (Exception $e) {
			DB::rollBack();
			throw new Exception("Erro ao atualizar fatura: " . $e->getMessage());
		}
	}

	public function deleteInvoice($id)
	{
		$model = Invoice::findOrFail($id);
		return $model->delete();
	}

	public function cancelInvoice($invoiceId)
	{
		$model = Invoice::find($invoiceId);
		if (!$model) {
			return false;
		}

		$model->status = 26;
		$model->save();

		// Cancela todos os webhooks pendentes relacionados a esta invoice
		$this->cancelPendingWebhooksByInvoice($invoiceId);

		return $model;
	}

	public function cancelPendingWebhooksByInvoice($invoiceId)
	{
		$webhooks = \App\Models\Webhook::where('invoice_id', $invoiceId)->where('status', 23)->get();
        foreach ($webhooks as $webhook) {
            $webhook->status = 26;
            $webhook->save();
        }
	}

	public function confirmPayment($invoiceId, $notifyClient = 1, $gatewayFee = 0)
	{
		DB::beginTransaction();

		try {
			$model = Invoice::find($invoiceId);
			if (!$model) {
				return false;
			}

			$model->status = 24;
			$model->paid_at = Carbon::now();
			$model->save();

			$paymentLocal = Integration::where('slug', 'pagamento-no-local')->first();

			// Verificar se é uma despesa pessoal
			$isExpense = false;

			// Verificar nos itens da fatura se algum está associado a um serviço pessoal
			foreach ($model->items as $item) {
				if ($item->target_type === 'user_service') {
					$userService = \App\Models\UserService::find($item->target_id);
					if ($userService) {
						$metadata = is_string($userService->metadata) ? json_decode($userService->metadata, true) : $userService->metadata;
						// Verificar se tem a flag de despesa ou se tem o tipo definido (que indica conta pessoal)
						if ((isset($metadata['is_expense']) && $metadata['is_expense'] === true) ||
							(isset($metadata['tipo']) && !empty($metadata['tipo']))
						) {
							$isExpense = true;
							break;
						}
					}
				}
			}

			$data_transaction = [
				'invoice_id' => $model->id,
				'integration_id' => $paymentLocal->id,
				'type' => $isExpense ? 'expense' : 'income',
				'amount' => $model->total,
				'gateway_fee' => $gatewayFee,
				'description' => $isExpense ?
					"Pagamento de despesa referente à fatura #{$model->id}" :
					"Pagamento confirmado para a fatura #{$model->id}",
				'date' => now(),
			];

			$this->transactionService->createTransaction($data_transaction);

			// Atualiza a data de vencimento dos serviços associados
			foreach ($model->items as $item) {
				// Verifica se o item está associado a um serviço de usuário
				if ($item->target_type === 'user_service') {
					// Busca o serviço do usuário
					$userService = \App\Models\UserService::find($item->target_id);

					if ($userService) {
						// Calcula a nova data de vencimento com base no período do serviço
						$newEndDate = $this->calculateNextEndDate($userService->end_date, $userService->period);

						// Atualiza a data de vencimento do serviço
						$userService->end_date = $newEndDate;
						$userService->save();
					}
				}
			}

			if ($notifyClient && $notifyClient == 1) {
				$notificationData = [
					'name' => $model->user->name,
					'invoice_id' => $model->id,
				];

				dispatch(new ProcessNotificationJob('email', $model->user->email, 'Invoice', 'email', 'payment_confirmed', $notificationData));
				if (isset($model->user->phone_ddi) && !empty($model->user->phone_ddi)) {
					dispatch(new ProcessNotificationJob('whatsapp', $model->user->phone_ddi, 'Invoice', 'whatsapp', 'payment_confirmed', $notificationData));
				}
			}

			DB::commit();
			return $model;
		} catch (\Exception $e) {
			DB::rollBack();
			throw new \Exception("Erro ao confirmar pagamento: " . $e->getMessage());
		}
	}

	public function sendReminder($invoiceId)
	{
		$model = Invoice::find($invoiceId);
		if (!$model) {
			return false;
		}

		$notificationData = [
			'name' => $model->user->name,
			'invoice_id' => $model->id,
			'due_date' => $model->due_at,
		];

		dispatch(new ProcessNotificationJob('email', $model->user->email, 'Invoice', 'email', 'invoice_reminder', $notificationData));
		if (isset($model->user->phone_ddi) && !empty($model->user->phone_ddi)) {
			dispatch(new ProcessNotificationJob('whatsapp', $model->user->phone_ddi, 'Invoice', 'whatsapp', 'invoice_reminder', $notificationData));
		}

		return $model;
	}

	/**
	 * Gera fatura recorrente para os serviços de um usuário ou para um serviço específico.
	 * Se anticipate = true, gera a próxima fatura mesmo que a data de término não tenha chegado.
	 * Se anticipate = false (padrão), só gera se a data de término já passou.
	 * Se serviceId for informado, gera apenas para esse serviço.
	 * Se notifyUser = true, envia notificação ao usuário.
	 * Opcionalmente, pode passar 'date' para referência de data.
	 */
	public function generateRecurringInvoiceForUserServices($userId, $options = [])
	{
		$anticipate = $options['anticipate'] ?? false;
		$dateReference = isset($options['date']) ? Carbon::parse($options['date']) : Carbon::now();
		$serviceId = $options['serviceId'] ?? null;
		$notifyUser = $options['notifyUser'] ?? false;

		$user = \App\Models\User::findOrFail($userId);

		// Busca serviços ativos e recorrentes
		$query = \App\Models\UserService::where('user_id', $userId)
			->where('status', 3)
			->where('period', '!=', 'once');
		if ($serviceId) {
			$query->where('id', $serviceId);
		}
		$userServices = $query->get();

		$items = [];
		$total = 0;
		$textServices = '';

		foreach ($userServices as $service) {
			// Decide se gera fatura vencida ou antecipada
			$shouldGenerate = false;
			if ($anticipate) {
				$shouldGenerate = true;
			} else {
				// Só gera se a data de término já passou
				$shouldGenerate = Carbon::parse($service->end_date)->lte($dateReference);
			}

			if ($shouldGenerate) {
				$nextEndDate = $this->calculateNextEndDate($service->end_date, $service->period);

				// Extrai metadados para obter o tipo específico da conta pessoal
				$metadata = is_string($service->metadata) ? json_decode($service->metadata, true) : $service->metadata;
				$hasTipo = isset($metadata['tipo']) && !empty($metadata['tipo']);
				$tipo = $hasTipo ? $metadata['tipo'] : null;
				$fornecedor = isset($metadata['fornecedor']) ? " ({$metadata['fornecedor']})" : "";

				if ($hasTipo) {
					$descricao = $tipo . $fornecedor . ' - ' .
						Carbon::parse($service->end_date)->format('d/m/Y') . ' até ' .
						$nextEndDate->format('d/m/Y');
					$textServices .= $descricao . "\n";
				} else {
					$descricao = $service->service->title . ' - ' .
						Carbon::parse($service->end_date)->format('d/m/Y') . ' até ' .
						$nextEndDate->format('d/m/Y');
					$textServices .= $descricao . "\n";
				}

				$items[] = [
					'service_id' => $service->id,
					'description' => $descricao,
					'quantity' => 1,
					'price_unit' => $service->price,
					'price_total' => $service->price,
				];
				$total += $service->price;
			}
		}

		if (empty($items)) {
			throw new \Exception('Nenhum serviço elegível para geração de fatura.');
		}

		// Cria a fatura
		$invoice = \App\Models\Invoice::create([
			'user_id' => $userId,
			'integration_id' => $user->integration_id ?? 4,
			'method_type' => $user->preferences && $user->preferences->payment_default ? $user->preferences->payment_default : 'pix',
			'total' => $total,
			'status' => 23, // Não pago
			'due_at' => Carbon::now()->addDays(7),
		]);

		foreach ($items as $item) {
			\App\Models\InvoiceItem::create([
				'invoice_id' => $invoice->id,
				'target_type' => 'user_service',
				'target_id' => $item['service_id'],
				'description' => $item['description'],
				'quantity' => $item['quantity'],
				'price_unit' => $item['price_unit'],
				'price_total' => $item['price_total'],
			]);
		}

		// Envia notificação ao cliente, se solicitado
		if ($notifyUser && $notifyUser == 'true') {
			$notificationData = [
				'name' => $user->name,
				'invoice_id' => $invoice->id,
				'invoice_total' => number_format($invoice->total, 2, ',', '.'),
				'invoice_due_date' => $invoice->due_at,
				'services' => $textServices,
			];

			// WhatsApp: dispara mensagem básica imediatamente
			if (isset($user->phone_ddi) && !empty($user->phone_ddi)) {
				dispatch(new ProcessNotificationJob('whatsapp', $user->phone_ddi, 'Invoice', 'whatsapp', 'invoice_new', $notificationData));
			}

			$result = [
				"payment_method" => $invoice->method_type,
				"name_holder" => $invoice->user->name,
				"document_holder" => $invoice->user->document,
			];

			$invoice = $this->getInvoiceById($invoice->id);
			$paymentMethod = $result['payment_method'] ?? $invoice->method_type;
			$responseData = $this->mercadoPagoIntegration->processPayment($invoice, $paymentMethod, $result);

			// Polling para aguardar o webhook
			$maxTries = 10;
			$interval = 1; // segundos
			$found = false;
			for ($i = 0; $i < $maxTries; $i++) {
				$invoice->refresh();
				if ($invoice->latestWebhook) {
					$found = true;
					break;
				}
				sleep($interval);
			}

			// Monta dados para o e-mail consolidado
			$notificationDataEmail = $notificationData;

			if ($found) {
				$invoiceLastWebhook = $invoice->latestWebhook;

				// PIX
				if (isset($invoiceLastWebhook->response_json['qr_code_base64']) && $invoiceLastWebhook->response_json['qr_code_base64'] != '') {
					$qrCode = $invoiceLastWebhook->response_json['qr_code'];
					$qrCodeBase64 = $invoiceLastWebhook->response_json['qr_code_base64'];
					$imageData = base64_decode($qrCodeBase64);
					$fileName = 'pix/invoice_' . $invoice->id . '_pix.png';
					Storage::disk('public')->put($fileName, $imageData);
					$imagePixPath = Storage::url($fileName);

					$notificationDataEmail['pix_qr_code'] = $qrCode;
					$notificationDataEmail['pix_qr_code_image'] = asset($imagePixPath);

					// WhatsApp: envia código e imagem separadamente
					$notificationDataPixImage = [ 'image' => asset($imagePixPath) ];
					$notificationDataPixCode = [ 'qr_code' => $qrCode ];
					if (isset($user->phone_ddi) && !empty($user->phone_ddi)) {
						dispatch(new ProcessNotificationJob('whatsapp', $user->phone_ddi, 'Invoice', 'whatsapp', 'invoice_new_pix_code', $notificationDataPixCode));
						dispatch(new ProcessNotificationJob('whatsapp', $user->phone_ddi, 'Invoice', 'whatsapp', 'invoice_new_pix_image', $notificationDataPixImage));
					}
				}

				// BOLETO
				if (isset($invoiceLastWebhook->response_json['boleto_digitable_line']) && $invoiceLastWebhook->response_json['boleto_digitable_line'] != '') {
					$boletoDigitableLine = $invoiceLastWebhook->response_json['boleto_digitable_line'];
					$boletoUrl = $invoiceLastWebhook->response_json['boleto_url'];
					$pdfContent = @file_get_contents($boletoUrl);
					$boletoFileName = 'boletos/invoice_' . $invoice->id . '_boleto.pdf';
					if ($pdfContent !== false) {
						Storage::disk('public')->put($boletoFileName, $pdfContent);
						$boletoPdfUrl = Storage::url($boletoFileName);
					} else {
						$boletoPdfUrl = null;
					}

					$notificationDataEmail['boleto_digitable_line'] = $boletoDigitableLine;
					$notificationDataEmail['boleto_pdf'] = asset($boletoPdfUrl);

					// WhatsApp: envia boleto em partes
					$notificationDataBoleto = [
						'name' => $invoice->user->name,
						'boleto_digitable_line' => $boletoDigitableLine,
					];
					$notificationDataBoletoPdf = [
						'document_pdf' => asset($boletoPdfUrl),
					];
					if (isset($user->phone_ddi) && !empty($user->phone_ddi)) {
						dispatch(new ProcessNotificationJob('whatsapp', $user->phone_ddi, 'Invoice', 'whatsapp', 'invoice_new_boleto_text', $notificationDataBoleto));
						dispatch(new ProcessNotificationJob('whatsapp', $user->phone_ddi, 'Invoice', 'whatsapp', 'invoice_new_boleto_code', $notificationDataBoleto));
						dispatch(new ProcessNotificationJob('whatsapp', $user->phone_ddi, 'Invoice', 'whatsapp', 'invoice_new_boleto_url', $notificationDataBoletoPdf));
					}
				}
			}

			// Dispara o e-mail consolidado (apenas uma vez, após coletar todos os dados)
			$emailTemplate = 'invoice_new';
			if (!empty($notificationDataEmail['pix_qr_code'])) {
				$emailTemplate = 'invoice_new_pix';
			} elseif (!empty($notificationDataEmail['boleto_digitable_line'])) {
				$emailTemplate = 'invoice_new_boleto';
			}
			dispatch(new ProcessNotificationJob('email', $user->email, 'Invoice', 'email', $emailTemplate, $notificationDataEmail));
		}

		return $invoice;
	}

	/**
	 * Calcula a próxima data de vencimento com base no período.
	 *
	 * @param string|\Carbon\Carbon $currentEndDate
	 * @param string $period
	 * @return \Carbon\Carbon
	 */
	public function calculateNextEndDate($currentEndDate, $period)
	{
		if ($currentEndDate instanceof Carbon) {
			$date = $currentEndDate;
		} else {
			try {
				if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $currentEndDate)) {
					$date = Carbon::createFromFormat('d/m/Y', $currentEndDate);
				} else {
					$date = Carbon::parse($currentEndDate);
				}
			} catch (\Exception $e) {
				$date = Carbon::now();
			}
		}

		switch ($period) {
			case 'monthly':
				return $date->copy()->addMonth();
			case 'quarterly':
				return $date->copy()->addMonths(3);
			case 'semiannual':
				return $date->copy()->addMonths(6);
			case 'annual':
				return $date->copy()->addYear();
			case 'biennial':
				return $date->copy()->addYears(2);
			default:
				return $date->copy()->addMonth();
		}
	}

	public function generateInvoiceFromUserService($userService, $dueDate = null, $customDescription = null, $customAmount = null)
	{
		DB::beginTransaction();

		try {
			// Obter o usuário e serviço associados
			$user = $userService->user;
			$service = $userService->service;

			// Definir data de vencimento (padrão: 3 dias a partir de hoje)
			$dueDate = $dueDate ?? Carbon::now()->addDays(3)->format('Y-m-d');

			// Obter integração padrão para pagamento
			$integration = Integration::where('slug', 'mercadopago')->first();
			
			// Determinar método de pagamento preferencial do cliente
			$defaultPaymentMethod = 'pix';
			if ($user->preferences && $user->preferences->payment_default) {
				$defaultPaymentMethod = $user->preferences->payment_default;
			}

			// Preparar descrição e valor
			$description = $customDescription ?? $service->title;
			$amount = $customAmount ?? $userService->price;

			// Dados da fatura
			$invoiceData = [
				'user_id' => $user->id,
				'integration_id' => $integration->id,
				'method_type' => $defaultPaymentMethod,
				'total' => $amount,
				'status' => 23, // Status 'Aguardando pagamento'
				'due_at' => $dueDate,
				'items' => [
					[
						'description' => $description,
						'quantity' => 1,
						'price_unit' => $amount,
						'price_total' => $amount
					]
				]
			];

			// Criar a fatura
			$invoice = $this->createInvoice($invoiceData);

			// Salvar referência ao serviço no item da fatura
			$invoiceItem = $invoice->items->first();
			$invoiceItem->target_type = 'user_service';
			$invoiceItem->target_id = $userService->id;
			$invoiceItem->save();

			DB::commit();
			return $invoice;
		} catch (Exception $e) {
			DB::rollBack();
			throw new Exception("Erro ao gerar fatura: " . $e->getMessage());
		}
	}
}
