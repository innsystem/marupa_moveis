<?php

namespace App\Integrations;

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
    protected $settings;

    public function __construct()
    {
        // Recupera a integração do Mercado Pago
        $integration = Integration::where('slug', 'mercadopago')->first();

        // Recupera o access-token da coluna JSON settings
        $accessTokenMercadopago = $integration ? $integration->settings['access_token'] ?? null : null;

        if (!$accessTokenMercadopago) {
            throw new \Exception('Access Token do Mercado Pago não encontrado.');
        }

        $this->settings = $integration->settings;

        // YOUR ACCESS TOKEN - MERCADOPAGO
        // $accessTokenMercadopago = 'TEST-1616954816199672-062817-14ba98cb6496fc0beb1ecd8008fff0e4-91042568';
        $accessTokenMercadopago = 'APP_USR-1616954816199672-062817-152046b603195e8789b3fe61bdc0f6da-91042568';
        $idempotencyKey = Uuid::uuid4()->toString();

        $this->version = 'v1/';
        $this->client = new Client([
            'base_uri' => 'https://api.mercadopago.com/',
            'headers' => [
                'X-Idempotency-Key' => $idempotencyKey,
                'Authorization' => 'Bearer ' . $accessTokenMercadopago,
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    public function settings()
    {
        return $this->settings;
    }

    // Gerar Token de Cartão
    public function generateCardToken($request)
    {
        $form = [
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
        ];

        try {
            $response = $this->client->post($this->version . 'card_tokens', [
                'json' => $form
            ]);

            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            return $this->handleClientException($e);
        } catch (RequestException $e) {
            return $this->handleRequestException($e);
        } catch (\Exception $e) {
            return $this->handleGeneralException($e);
        }
    }

    public function addCardCustomer($request)
    {
        $form = [
            'token' => $request['token'],
            'customer_id' => $request['customer_id'],
        ];

        try {
            $response = $this->client->post($this->version . 'customers/' . $request['customer_id'] . '/cards', [
                'json' => $form
            ]);

            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            return $this->handleClientException($e);
        } catch (RequestException $e) {
            return $this->handleRequestException($e);
        } catch (\Exception $e) {
            return $this->handleGeneralException($e);
        }
    }

    // Criar Novo Cliente
    public function createCustomer($request)
    {
        $form = [
            'first_name' => $request['first_name'],
            'email' => $request['email'],
        ];

        // dd($form);

        try {
            $response = $this->client->post($this->version . 'customers', [
                'json' => $form
            ]);

            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            return $this->handleClientException($e);
        } catch (RequestException $e) {
            return $this->handleRequestException($e);
        } catch (\Exception $e) {
            return $this->handleGeneralException($e);
        }
    }

    // Recupera Cliente
    public function searchCustomer($email)
    {
        try {
            $response = $this->client->get($this->version . "customers/search?email=$email");

            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            return $this->handleClientException($e);
        } catch (RequestException $e) {
            return $this->handleRequestException($e);
        } catch (\Exception $e) {
            return $this->handleGeneralException($e);
        }
    }

    // Obter Cartão Cliente
    public function getCardCustomer($customer_id, $card_id)
    {
        try {
            $response = $this->client->get($this->version . "customers/$customer_id/cards/$card_id");

            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            return $this->handleClientException($e);
        } catch (RequestException $e) {
            return $this->handleRequestException($e);
        } catch (\Exception $e) {
            return $this->handleGeneralException($e);
        }
    }

    // Obter Pedido
    public function getOrder($preference_id)
    {
        try {
            $response = $this->client->get($this->version . 'payments/' . $preference_id);
            // \Log::info('preference:'. $preference_id);
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            if ($e->getCode() == 0) {
                \Log::info('MercadoPago getOrder :: INFO código' . $e->getCode() . ' na requisição GuzzleHttp: ' . $e->getMessage());
                return '0';
            } else {
                \Log::error('MercadoPago getOrder :: ERROR código' . $e->getCode() . ' na requisição GuzzleHttp: ' . $e->getMessage());
            }

            return '404';
        }
    }

    // Obter Preferencia
    public function getPreference($preference_id)
    {
        try {
            $response = $this->client->get('checkout/preferences/' . $preference_id);
            // \Log::info('preference:'. $preference_id);
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            if ($e->getCode() == 0) {
                \Log::info('MercadoPago getOrder :: INFO código' . $e->getCode() . ' na requisição GuzzleHttp: ' . $e->getMessage());
                return '0';
            } else {
                \Log::error('MercadoPago getOrder :: ERROR código' . $e->getCode() . ' na requisição GuzzleHttp: ' . $e->getMessage());
            }

            return '404';
        }
    }

    // Obter Link Pedido
    public function getOrderLink($preference_id)
    {
        try {
            $response = $this->client->get($this->version . 'payments/' . $preference_id);
            // \Log::info('preference:'. $preference_id);

            $payment = json_decode($response->getBody(), true);

            return $payment['point_of_interaction']['transaction_data']['ticket_url'];
        } catch (\Exception $e) {
            if ($e->getCode() == 0) {
                \Log::info('MercadoPago getOrderLink :: INFO código' . $e->getCode() . ' na requisição GuzzleHttp: ' . $e->getMessage());
                return '0';
            } else {
                \Log::error('MercadoPago getOrderLink :: ERROR código' . $e->getCode() . ' na requisição GuzzleHttp: ' . $e->getMessage());
            }

            return '404';
        }
    }

    // Cria um Novo Pagamento
    public function createPayment(array $payload)
    {
        try {
            $response = $this->client->post($this->version . 'payments', [
                'json' => $payload
            ]);

            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            return $this->handleClientException($e);
        } catch (RequestException $e) {
            return $this->handleRequestException($e);
        } catch (\Exception $e) {
            return $this->handleGeneralException($e);
        }
    }

    // Cria uma Nova Preferencia de Pagamento
    public function createPreference(array $payload)
    {
        try {
            $response = $this->client->post('checkout/preferences', [
                'json' => $payload
            ]);

            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            return $this->handleClientException($e);
        } catch (RequestException $e) {
            return $this->handleRequestException($e);
        } catch (\Exception $e) {
            return $this->handleGeneralException($e);
        }
    }

    // recupera os metodos de pagamento
    public function getPaymentMethods()
    {
        try {
            $response = $this->client->get($this->version . 'payment_methods');
            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            return $this->handleClientException($e);
        } catch (RequestException $e) {
            return $this->handleRequestException($e);
        } catch (\Exception $e) {
            return $this->handleGeneralException($e);
        }
    }
    // lista os metodos de pagamento com cartão de crédito (ex: visa, mastercard, etc)
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

    // recupera informações do cartão
    public function getBinCard($card_bin)
    {
        try {
            // Pega os primeiros 6 dígitos do cartão
            $bin = substr($card_bin, 0, 6);

            $brands = [
                'visa' => ['4'],
                'master' => ['51', '52', '53', '54', '55', '2221', '2720'],
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
        } catch (ClientException $e) {
            return $this->handleClientException($e);
        } catch (RequestException $e) {
            return $this->handleRequestException($e);
        } catch (\Exception $e) {
            return $this->handleGeneralException($e);
        }
    }

    // recupera as parcelas
    public function getInstallments($amount, $card_brand)
    {
        try {
            $response = $this->client->get($this->version . 'payment_methods/installments?amount=' . $amount . '&payment_method_id=' . $card_brand ?? 'visa');

            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            return $this->handleClientException($e);
        } catch (RequestException $e) {
            return $this->handleRequestException($e);
        } catch (\Exception $e) {
            return $this->handleGeneralException($e);
        }
    }

    // calculo de parcelas e juros
    public function calcularParcelas($invoice_total, $card_brand)
    {
        // Obtém as parcelas diretamente da API do Mercado Pago
        $arrayMercadoPagoInstallments = $this->getInstallments($invoice_total, $card_brand);

        // Configurações do sistema
        $taxaCartao = (float) $this->settings['fee_credit_card']; // Tarifa de processamento (ex.: 4.98%)
        $maxParcelas = (int) $this->settings['max_installments']; // Máximo de parcelas permitido (ex.: 8)
        $parcelasSemJuros = (int) $this->settings['installments_free']; // Número de parcelas sem juros (ex.: 2)

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
        ];

        return [
            'status' => $status,
            'message' => $paymentData['message'] . ' ' . $paymentData['message_reason'],
            'response_json' => $paymentData,
            'http_code' => ($status == 'approved' || $status == 'paid' || 'pending') ? 200 : 422,
        ];
    }


    // Tratamentos de Erros
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
}
