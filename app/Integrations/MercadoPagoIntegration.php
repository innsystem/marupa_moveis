<?php

namespace App\Integrations;

use Illuminate\Support\Facades\Validator;
use App\Models\Integration;
use App\Services\WebhookService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Ramsey\Uuid\Uuid;

class MercadoPagoIntegration
{
    protected $client;
    protected $version;

    // Constructor
    public function __construct()
    {
        $accessTokenMercadopago = $this->getAccessToken();

        if (!$accessTokenMercadopago) {
            throw new \Exception('Access Token do Mercado Pago não encontrado.');
        }

        $this->version = 'v1/';
        $idempotencyKey = $this->generateIdempotencyKey();

        $this->client = new Client([
            'base_uri' => 'https://api.mercadopago.com/',
            'headers' => [
                'X-Idempotency-Key' => $idempotencyKey,
                'Authorization' => 'Bearer ' . $accessTokenMercadopago,
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    // Gera Idempotency Key
    public function generateIdempotencyKey()
    {
        return Uuid::uuid4()->toString();
    }

    // Recupera Integração
    public function getIntegration()
    {
        $integration = Integration::where('slug', 'mercadopago')->first();
        return $integration;
    }

    // Recupera Access Token
    public function getAccessToken()
    {
        $integration = $this->getIntegration();
        return $integration->settings['access_token'] ?? throw new \Exception('Access Token do Mercado Pago não encontrado.');
    }

    // Obter Configurações
    public function getSettings()
    {
        $integration = $this->getIntegration();
        return $integration->settings ?? [];
    }

    // Enviar Requisição
    public function sendRequest($method, $version, $uri, $body  = [])
    {
        try {
            $response = $this->client->request($method, $version . $uri, $body);
            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            return $this->handleClientException($e);
        } catch (RequestException $e) {
            return $this->handleRequestException($e);
        } catch (\Exception $e) {
            return $this->handleGeneralException($e);
        }
    }

    // Gerar Token de Cartão
    public function generateCardToken($request)
    {
        return $this->sendRequest('post', $this->version, 'card_tokens', [
            'json' => [
                'card_number' => $request['card_number'],
                'expiration_month' => $request['expiration_month'],
                'expiration_year' => $request['expiration_year'],
                'security_code' => $request['security_code'],
                'cardholder' => [
                    'name' => $request['cardholder']['name'],
                    'identification' => [
                        'type' => $request['cardholder']['identification']['type'],
                        'number' => $request['cardholder']['identification']['number'],
                    ],
                ],
            ]
        ]);
    }

    public function addCardCustomer($request)
    {
        $customer_id = $request['customer_id'];

        return $this->sendRequest('post', $this->version, "customers/$customer_id/cards", [
            'json' => [
                'token' => $request['token'],
                'customer_id' => $request['customer_id'],
            ]
        ]);
    }

    // Criar Novo Cliente
    public function createCustomer($request)
    {
        return $this->sendRequest('post', $this->version, 'customers', [
            'json' => [
                'first_name' => $request['first_name'],
                'email' => $request['email'],
            ]
        ]);
    }

    // Recupera Cliente
    public function searchCustomer($email)
    {
        return $this->sendRequest('get', $this->version, "customers/search?email=$email");
    }

    // Obter Cartão Cliente
    public function getCardCustomer($customer_id, $card_id)
    {
        return $this->sendRequest('get', $this->version, "customers/$customer_id/cards/$card_id");
    }

    // Obter Pedido
    public function getOrder($preference_id)
    {
        return $this->sendRequest('get', $this->version, "payments/" . $preference_id);
    }

    // Obter Preferencia
    public function getPreference($preference_id)
    {
        return $this->sendRequest('get', null, "checkout/preferences/" . $preference_id);
    }

    // Cria um Novo Pagamento
    public function createPayment(array $payload)
    {
        return $this->sendRequest('post', $this->version, 'payments', [
            'json' => $payload
        ]);
    }

    // Processar Pagamento
    public function processPayment($invoice, $paymentMethod, $request)
    {
        // Updated the invoice with the payment method
        $invoice->method_type = $paymentMethod;
        $invoice->save();

        $items = [];
        $transaction_amount = 0;

        foreach ($invoice->items as $key => $item_row) {
            $items[] = [
                'id' => $key + 1,
                'title' => $item_row['description'],
                'description' => 'O pagamento desse item confirma o ' . $item_row['description'],
                'quantity' => (int) $item_row['quantity'],
                'unit_price' => (float) $item_row['price_unit'],
                'category_id' => 'professional_services',
                "warranty" => false,
                "category_descriptor" => [
                    "passenger" => [],
                    "route" => []
                ]
            ];

            $transaction_amount += (int) $item_row['quantity'] * (float) $item_row['price_unit'];
        }

        $getUser = $invoice->user;
        $getAddressUser = $getUser->address();

        $payer = [
            "first_name" => $getUser->name,
            "last_name" => "Cliente",
            "phone" => $this->formatPhone($getUser->phone),
        ];

        $payer_data = [
            "entity_type" => "individual",
            "type" => "customer",
            "first_name" => $getUser->name,
            "last_name" => "Cliente",
            "email" => $getUser->email,
            "identification" => [
                "type" => strlen($this->formatDocument($getUser->document ?? $request['document_holder'])) < 13 ? 'CPF' : 'CNPJ',
                "number" => $this->formatDocument($getUser->document ?? $request['document_holder'])
            ],
            "address" => [
                "zip_code" => $getAddressUser->zipcode,
                "street_name" => $getAddressUser->street,
                "street_number" => $getAddressUser->number,
                "neighborhood" => $getAddressUser->district,
                "city" => $getAddressUser->city,
                "federal_unit" => $getAddressUser->state,
            ]
        ];

        if ($paymentMethod == 'pix') {
            $createRequest = [
                "additional_info" => [
                    "items" => $items,
                    "payer" => $payer,
                ],
                "description" => "Pagamento da Fatura #" . $invoice->id,
                "external_reference" => "invoice." . $invoice->id,
                "installments" => 1,
                "payer" => $payer_data,
                "payment_method_id" => 'pix',
                "transaction_amount" => round((float) $transaction_amount, 2),
                "notification_url" => 'https://innsystem.com.br/webhook/mercadopago', // URL de notificação
            ];
        }

        if ($paymentMethod == 'boleto') {
            $createRequest = [
                "additional_info" => [
                    "items" => $items,
                    "payer" => $payer,
                ],
                "description" => "Pagamento da Fatura #" . $invoice->id,
                "external_reference" => "invoice." . $invoice->id,
                "installments" => 1,
                "payer" => $payer_data,
                "payment_method_id" => 'bolbradesco',
                "transaction_amount" => round((float) $transaction_amount, 2),
                "notification_url" => 'https://innsystem.com.br/webhook/mercadopago', // URL de notificação
            ];
        }

        if ($paymentMethod == 'credit_card') {
            $rules = [
                'name_holder' => 'required',
                'document_holder' => 'required',
                'card_number' => 'required',
                'expiration_month' => 'required',
                'expiration_year' => 'required',
                'security_code' => 'required',
            ];

            $messages = [
                'name_holder.required' => 'Nome do titular é obrigatório',
                'document_holder.required' => 'Documento do titular é obrigatório',
                'card_number.required' => 'Número do cartão é obrigatório',
                'expiration_month.required' => 'Mês de expiração é obrigatório',
                'expiration_year.required' => 'Ano de expiração é obrigatório',
                'security_code.required' => 'Código de segurança é obrigatório',
            ];

            $validator = Validator::make($request, $rules, $messages);

            if ($validator->fails()) {
                return response()->json($validator->errors()->first(), 422);
            }

            // // Atualiza o Documento do Cliente
            // $getUser->document = $this->formatDocument($getUser->document);
            // $getUser->save();

            // $getUser->refresh();

            // Cria Cliente
            $user_data = [
                'first_name' => $getUser->name,
                'email' => $getUser->email,
            ];

            $customer_id = null;
            $user_create = $this->createCustomer($user_data);

            if (isset($user_create['error'])) {
                if (isset($user_create['message']['cause']) && $user_create['message']['cause'][0]['code'] == '101') {
                    $getCustomer = $this->searchCustomer($user_data['email']);
                    foreach ($getCustomer['results'] as $customer) {
                        $customer_id = $customer['id'];
                    }
                } else {
                    return response()->json($user_create['message'], 422);
                }
            }

            // Tokeniza Cartão
            $token_card = null;
            $bin = substr(preg_replace('/\D/', '', $this->formatDocument($request['card_number'])), 0, 6);

            $user_card = [
                'card_number' => $this->formatDocument($request['card_number']),
                'expiration_month' => $request['expiration_month'],
                'expiration_year' => $request['expiration_year'],
                'security_code' => $request['security_code'],
                'cardholder' => [
                    'name' => $request['name_holder'],
                    'identification' => [
                        'type' => strlen($this->formatDocument($request['document_holder'])) < 13 ? 'CPF' : 'CNPJ',
                        'number' => $this->formatDocument($request['document_holder']),
                    ],
                ],
                'bin' => $bin,
            ];

            $generateCard = $this->generateCardToken($user_card);

            if (isset($generateCard['error'])) {
                if (isset($generateCard['message']['cause'])) {
                    return response()->json($generateCard['message']['cause'][0]['description'], 422);
                } else {
                    return response()->json($generateCard['message']['message'], 422);
                }
            }

            $token_card = $generateCard['id'];

            // Adiciona o cartão ao cliente
            $card_related = [
                'customer_id' => $customer_id,
                'token' => $token_card,
            ];

            $this->addCardCustomer($card_related);

            $integration = $this->getIntegration();
            $webhook_url = $integration->settings['webhook_url'] ?? '';

            if (isset($getAddressUser)) {
                $shipment = [
                    "receiver_address" => [
                        "zip_code" => $getAddressUser->zipcode,
                        "state_name" => $getAddressUser->state,
                        "city_name" => $getAddressUser->city,
                        "street_name" => $getAddressUser->address,
                        "street_number" => $getAddressUser->number,
                    ]
                ];
            }

            $createRequest = [
                "additional_info" => [
                    "items" => $items,
                    "payer" => $payer,
                    // "shipments" => $shipment,
                ],
                "description" => "Pagamento da Fatura #" . $invoice->id,
                "external_reference" => "invoice." . $invoice->id,
                "installments" => (int) $request['installments'] ?? 1,
                "payer" => $payer_data,
                "statement_descriptor" => $getSettings['site_name'] ?? 'SITE',
                "payment_method_id" => $request['brand_card'] ?? 'master',
                "token" => $token_card, // Token do Cartão
                "transaction_amount" => round((float) $transaction_amount, 2),
                "notification_url" => $webhook_url, // URL de notificação
            ];
        }

        \Log::info('createRequest = ' . json_encode($createRequest));

        try {
            $response = $this->createPayment($createRequest);

            \Log::info('response = ' . json_encode($response));

            if (isset($response['error'])) {
                \Log::error('Erro ao criar pagamento: ' . json_encode($response));
                return response()->json($response['message']['cause'][0]['description'], 422);
            }

            $paymentResult = $this->handlePaymentResponse($response, $invoice);

            \Log::info('paymentResult = ' . json_encode($paymentResult));

            $webhookData = [
                'invoice_id' => $invoice->id,
                'integration_id' => $invoice->integration_id,
                'payment_code' => $response['id'],
                'status' => ($paymentResult['status'] === 'rejected') ? 26 : 23, // Cancelado / Pendente
                'response_json' => $paymentResult['response_json'],
            ];

            app(\App\Services\WebhookService::class)->createWebhook($webhookData);

            return [
                'status' => $paymentResult['status'],
                'message' => $paymentResult['message'],
                'status_http' => $paymentResult['http_code'],
                'payment_method' => $paymentMethod,
            ];
        } catch (\Exception $e) {
            return response()->json('Erro ao processar pagamento: ' . $e->getMessage());
        }
    }

    // Cria uma Nova Preferencia de Pagamento
    public function createPreference(array $payload)
    {
        return $this->sendRequest('post', null, 'checkout/preferences', [
            'json' => $payload
        ]);
    }

    // recupera os metodos de pagamento
    public function getPaymentMethods()
    {
        return $this->sendRequest('get', $this->version, 'payment_methods');
    }

    // formata os metodos de pagamento apenas com cartão de crédito (ex: visa, mastercard, etc)
    public function listPaymentMethods()
    {
        $getMethods = $this->getPaymentMethods();

        $methods = [];

        foreach ($getMethods as $method) {
            if ($method['payment_type_id'] == 'credit_card' && $method['status'] == 'active') {
                $methods[] = [
                    'id' => $method['id'],
                    'name' => $method['name'],
                    'thumbnail' => $method['thumbnail']
                ];
            }
        }

        return $methods;
    }

    // busca a bandeira do cartão
    public function getBinCard($card_bin)
    {
        // Pega os primeiros 6 dígitos do cartão
        $bin = substr($card_bin, 0, 6);

        $brands = [
            'visa' => ['4'],
            'master' => ['51', '52', '53', '54', '55', '2221', '2720', '5031'],
            'amex' => ['34', '37'],
            'elo' => ['401178', '401179', '431274', '438935', '457631', '457632', '504175', '627780', '636297', '636368'],
            'hipercard' => ['606282', '384100', '384140', '384160'],
            'diners' => ['300', '301', '302', '303', '304', '305', '36', '38'],
            'discover' => ['6011', '622', '64', '65'],
            'jcb' => ['35'],
        ];

        foreach ($brands as $brand => $prefixes) {
            foreach ($prefixes as $prefix) {
                if (strpos($bin, $prefix) === 0) {
                    return $brand;
                }
            }
        }

        return null;
    }

    // recupera as parcelas
    public function getInstallments($amount, $card_brand)
    {
        return $this->sendRequest('get', $this->version, 'payment_methods/installments?amount=' . $amount . '&payment_method_id=' . $card_brand ?? 'visa');
    }

    // calculo de parcelas e juros
    public function calcularParcelas($invoice_total, $card_brand)
    {
        // Obtém as parcelas diretamente da API do Mercado Pago
        $arrayMercadoPagoInstallments = $this->getInstallments($invoice_total, $card_brand);

        // Configurações do sistema
        $settingsIntegration = $this->getSettings();
        $taxaCartao = (float) $settingsIntegration['fee_credit_card']; // Tarifa de processamento (ex.: 4.98%)
        $maxParcelas = (int) $settingsIntegration['max_installments']; // Máximo de parcelas permitido (ex.: 8)
        $parcelasSemJuros = (int) $settingsIntegration['installments_free']; // Número de parcelas sem juros (ex.: 2)

        $parcelas = [];

        // Filtrar a primeira opção de pagamento válida (visa, master, etc.)
        foreach ($arrayMercadoPagoInstallments as $installmentData) {
            if (!empty($installmentData['payer_costs'])) {
                foreach ($installmentData['payer_costs'] as $parcelamento) {
                    $numParcelas = $parcelamento['installments'];
                    $valorParcela = (float) $parcelamento['installment_amount'];
                    $valorTotal = (float) $parcelamento['total_amount'];

                    // Respeitar o limite de parcelas configurado no sistema
                    if ($numParcelas <= $maxParcelas) {
                        $parcelas[$numParcelas] = [
                            'parcela' => $numParcelas,
                            'valor_parcela' => number_format($valorParcela, 2, ',', '.'),
                            'valor_total' => number_format($valorTotal, 2, ',', '.'),
                            'juros' => $numParcelas > $parcelasSemJuros ? 'com' : 'sem',
                        ];
                    }
                }
                break; // Pega apenas a primeira opção válida
            }
        }

        return $parcelas;
    }

    // Tratação de erros e mensagens
    public function handlePaymentResponse($response, $invoice = null)
    {
        // Mapeando os status e detalhes do pagamento
        $statusMessages = [
            'pending' => 'Seu pagamento está pendente.',
            'approved' => 'Pagamento aprovado! Seu pedido foi processado com sucesso.',
            'authorized' => 'Pagamento autorizado, mas ainda não capturado.',
            'in_process' => 'Seu pagamento está em análise.',
            'in_mediation' => 'O pagamento está em disputa. Aguarde a resolução.',
            'rejected' => 'O pagamento foi recusado. Verifique os detalhes do cartão.',
            'cancelled' => 'O pagamento foi cancelado ou expirou.',
            'refunded' => 'O pagamento foi reembolsado com sucesso.',
            'charged_back' => 'O pagamento teve um chargeback aplicado.',
        ];

        $rejectionReasons = [
            'cc_rejected_bad_filled_card_number' => 'Número do cartão inválido. Verifique e tente novamente.',
            'cc_rejected_bad_filled_date' => 'Data de validade do cartão inválida. Verifique e tente novamente.',
            'cc_rejected_bad_filled_other' => 'Os dados do cartão parecem incorretos. Verifique e tente novamente.',
            'cc_rejected_bad_filled_security_code' => 'Código de segurança inválido. Verifique e tente novamente.',
            'cc_rejected_blacklist' => 'O pagamento foi recusado por segurança. Contate seu banco.',
            'cc_rejected_call_for_authorize' => 'O banco exige autorização para esta compra. Contate seu banco.',
            'cc_rejected_card_disabled' => 'O cartão está desativado. Contate seu banco.',
            'cc_rejected_card_error' => 'Erro no processamento do cartão. Tente novamente.',
            'cc_rejected_duplicated_payment' => 'Parece que esse pagamento já foi realizado.',
            'cc_rejected_high_risk' => 'O pagamento foi recusado por medidas de segurança.',
            'cc_rejected_insufficient_amount' => 'Saldo insuficiente no cartão.',
            'cc_rejected_invalid_installments' => 'Parcelamento não permitido para este cartão.',
            'cc_rejected_max_attempts' => 'Número máximo de tentativas excedido. Tente novamente mais tarde.',
            'cc_rejected_other_reason' => 'O pagamento foi recusado. Tente novamente ou use outro cartão.',

            'Invalid user identification number' => 'Número de identificação do usuário inválido.',
        ];

        $status = $response['status'] ?? 'unknown';
        $statusDetail = $response['status_detail'] ?? null;

        $paymentData = [
            'status' => $status,
            'status_detail' => $statusDetail,
            'qr_code' => $response['point_of_interaction']['transaction_data']['qr_code'] ?? null,
            'qr_code_base64' => $response['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null,
            'ticket_url' => $response['point_of_interaction']['transaction_data']['ticket_url'] ?? null,
            'message' => $statusMessages[$status] ?? 'Mensagem desconhecida.',
            'message_reason' => $rejectionReasons[$statusDetail] ?? '',
            'boleto_digitable_line' => $response['transaction_details']['digitable_line'] ?? null,
            'boleto_barcode' => $response['barcode']['content'] ?? null,
            'boleto_url' => $response['transaction_details']['external_resource_url'] ?? null,
        ];

        return [
            'status' => $status,
            'message' => $paymentData['message'] . ' ' . $paymentData['message_reason'],
            'response_json' => $paymentData,
            'http_code' => ($status == 'approved' || $status == 'paid' || 'pending') ? 200 : 422,
        ];
    }

    private function handleClientException(ClientException $e)
    {
        $responseBody = json_decode($e->getResponse()->getBody()->getContents(), true);
        return [
            'type' => 'ClientException',
            'error' => true,
            'message' => $responseBody
        ];
    }

    private function handleRequestException(RequestException $e)
    {
        return [
            'type' => 'RequestException',
            'error' => true,
            'message' => $e,
        ];
    }

    private function handleGeneralException(\Exception $e)
    {
        return [
            'type' => 'GeneralException',
            'error' => true,
            'message' => $e,
        ];
    }


    public function formatDocument($document)
    {
        return preg_replace('/\D/', '', $document);
    }

    public function formatPhone($phone)
    {
        // Remove tudo que não for número
        $phone = preg_replace('/\D/', '', $phone);

        // Verifica se o telefone tem pelo menos 10 dígitos
        if (strlen($phone) >= 10) {
            return [
                "area_code" => (int) substr($phone, 0, 2),
                "number" => substr($phone, 2)
            ];
        }

        // Retorna null se o telefone for inválido
        return [
            "area_code" => null,
            "number" => null
        ];
    }
}
