<?php

namespace App\Integrations;

use Illuminate\Support\Facades\Validator;
use App\Models\Integration;
use App\Models\ProductAffiliateLink;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class ShopeeIntegration
{
    protected $client;
    protected $appId;
    protected $secret;
    protected $baseUri;
    protected $baseUriSeller;

    public function __construct()
    {
        // Garante que o timezone do servidor está correto
        date_default_timezone_set('UTC');

        $settings = $this->getSettings();

        if (!$settings) {
            throw new \Exception('Credenciais da Shopee não encontradas.');
        }

        $this->appId = $settings['app_id'];
        $this->secret = $settings['secret_key'];
        $this->baseUri = 'https://open-api.affiliate.shopee.com.br/graphql';
        $this->baseUriSeller = 'https://partner.shopeemobile.com/api/v2';

        $this->client = new Client([
            // 'base_uri' => $this->baseUri,
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    public function getIntegration()
    {
        return Integration::where('slug', 'shopee')->first();
    }

    public function getSettings()
    {
        $integration = $this->getIntegration();
        return $integration->settings ?? null;
    }

    private function generateSignature($payload, $timestamp)
    {
        // JSON corretamente formatado para assinatura
        $payloadString = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // String de assinatura seguindo a documentação
        $signatureString = $this->appId . $timestamp . $payloadString . $this->secret;

        // Hash SHA256
        return hash('sha256', $signatureString);
    }

    public function sendRequestAffiliate($query, $operationName = null, $variables = [])
    {
        $payload = [
            'query' => $query,
            'operationName' => $operationName,
            'variables' => (object) $variables
        ];

        $timestamp = time();
        $signature = $this->generateSignature($payload, $timestamp);

        $headers = [
            'Authorization' => "SHA256 Credential={$this->appId}, Timestamp={$timestamp}, Signature={$signature}",
            'Content-Type'  => 'application/json'
        ];

        try {
            $response = $this->client->request('POST', $this->baseUri, [
                'headers' => $headers,
                'body'    => json_encode($payload) // Garante que o corpo seja JSON válido
            ]);

            // \Log::info('Resposta da API da Shopee: ' . $response->getBody());

            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            \Log::error('Erro ao enviar requisição para a API da Shopee: ' . $e->getMessage());
            return $this->handleClientException($e);
        } catch (RequestException $e) {
            \Log::error('Erro ao enviar requisição para a API da Shopee: ' . $e->getMessage());
            return $this->handleRequestException($e);
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar requisição para a API da Shopee: ' . $e->getMessage());
            return $this->handleGeneralException($e);
        }
    }

    public function getShopeeOffers($keyword = "", $page = 1, $limit = 10)
    {
        $query = <<<GQL
        query GetShopeeOffers(\$keyword: String, \$page: Int, \$limit: Int) {
            shopeeOfferV2(keyword: \$keyword, page: \$page, limit: \$limit) {
                nodes {
                    commissionRate
                    imageUrl
                    offerLink
                    originalLink
                    offerName
                    offerType
                    categoryId
                    collectionId
                    periodStartTime
                    periodEndTime
                }
                pageInfo {
                    page
                    limit
                    hasNextPage
                }
            }
        }
        GQL;

        $variables = [
            "keyword" => $keyword,
            "page" => $page,
            "limit" => $limit
        ];

        return $this->sendRequestAffiliate($query, "GetShopeeOffers", $variables);
    }

    public function getShopOffers($keyword = "", $shopId = null, $shopType = [1, 2, 4], $isKeySeller = null, $sortType = 1, $sellerCommCoveRatio = "", $page = 1, $limit = 10)
    {
        $query = <<<GQL
        query GetShopOffers(\$keyword: String, \$shopId: Int64, \$shopType: [Int!], \$isKeySeller: Boolean, \$sortType: Int, \$sellerCommCoveRatio: String, \$page: Int, \$limit: Int) {
            shopOfferV2(
                keyword: \$keyword,
                shopId: \$shopId,
                shopType: \$shopType,
                isKeySeller: \$isKeySeller,
                sortType: \$sortType,
                sellerCommCoveRatio: \$sellerCommCoveRatio,
                page: \$page,
                limit: \$limit
            ) {
                nodes {
                    shopId
                    shopName
                    imageUrl
                    commissionRate
                    offerLink
                    originalLink
                    ratingStar
                    shopType
                    remainingBudget
                    periodStartTime
                    periodEndTime
                    sellerCommCoveRatio
                }
                pageInfo {
                    page
                    limit
                    hasNextPage
                }
            }
        }
        GQL;

        // Certificar que shopType sempre é um array válido
        $shopType = array_values(array_filter($shopType, fn($value) => !is_null($value)));

        $variables = [
            "keyword" => $keyword,
            "shopId" => $shopId,
            "shopType" => $shopType,
            "isKeySeller" => $isKeySeller,
            "sortType" => $sortType,
            "sellerCommCoveRatio" => $sellerCommCoveRatio,
            "page" => $page,
            "limit" => $limit
        ];

        return $this->sendRequestAffiliate($query, "GetShopOffers", $variables);
    }

    public function getProductsOffers($keyword = null, $itemId = null, $productCatId = null, $sortType = 2, $page = 1, $limit = 10)
    {
        // Converte itemId para string
        if ($itemId) {
            $itemId = (string) $itemId; // Passa como string para garantir que seja um número grande
        }

        if (empty($itemId)) {
            $itemId = null; // Deixa null se não for fornecido um itemId
        }

        $query = <<<GQL
        query GetProductsOffers(\$keyword: String, \$itemId: Int64, \$productCatId: Int, \$sortType: Int, \$page: Int, \$limit: Int) {
            productOfferV2(keyword: \$keyword, itemId: \$itemId, productCatId: \$productCatId, sortType: \$sortType, page: \$page, limit: \$limit) {
                nodes {
                    itemId
                    productCatIds
                    productName
                    imageUrl
                    priceMin
                    priceMax
                    commissionRate
                    sales
                    ratingStar
                    productLink
                    offerLink
                }
                pageInfo {
                    page
                    limit
                    hasNextPage
                }
            }
        }
        GQL;

        // Criar array de variáveis sem incluir `productCatId` se for `null`
        $variables = [
            "page" => $page,
            "limit" => $limit
        ];

        if (!is_null($keyword)) {
            $variables["keyword"] = $keyword;
        }

        if (!is_null($itemId)) {
            $variables["itemId"] = $itemId; // Garante que seja um Int
        }
        if (!is_null($productCatId)) {
            $variables["productCatId"] = (int) $productCatId; // Garante que seja um Int
        }
        if (!is_null($sortType)) {
            $variables["sortType"] = (int) $sortType; // Garante que seja um Int
        }

        return $this->sendRequestAffiliate($query, "GetProductsOffers", $variables);
    }

    public function normalizeInCategories($results)
    {
        if (!isset($results['data']['shopeeOfferV2']['nodes'])) {
            return [];
        }

        return array_map(function ($item) {
            return [
                'name' => str_replace('20221014 -  KOL - 2022 - ', '', $item['offerName']) ?? 'Sem nome',
                'category_id' => $item['categoryId'] ?? null,
            ];
        }, $results['data']['shopeeOfferV2']['nodes']);
    }

    public function normalizeShopeeOffers($results)
    {
        if (!isset($results['data']['shopeeOfferV2']['nodes'])) {
            return [];
        }

        return array_map(function ($item) {
            return [
                'name' => $item['offerName'] ?? 'Sem nome',
                'image' => $item['imageUrl'] ?? '',
                'commission' => $item['commissionRate'] ?? '0',
                'offer_link' => $item['offerLink'] ?? '',
                'original_link' => $item['originalLink'] ?? '',
                'category_id' => $item['categoryId'] ?? null,
                'period_start' => date('d/m/Y', $item['periodStartTime'] ?? time()),
                'period_end' => date('d/m/Y', $item['periodEndTime'] ?? time()),
            ];
        }, $results['data']['shopeeOfferV2']['nodes']);
    }

    public function normalizeShopOffers($results)
    {
        if (!isset($results['data']['shopOfferV2']['nodes'])) {
            return [];
        }

        return array_map(function ($item) {
            return [
                'shop_id' => $item['shopId'] ?? null,
                'shop_name' => $item['shopName'] ?? 'Loja sem nome',
                'image' => $item['imageUrl'] ?? '',
                'commission' => $item['commissionRate'] ?? '0',
                'offer_link' => $item['offerLink'] ?? '',
                'original_link' => $item['originalLink'] ?? '',
                'rating' => $item['ratingStar'] ?? 'N/A',
                'shop_type' => implode(', ', $item['shopType'] ?? []),
                'remaining_budget' => $this->formatBudget($item['remainingBudget'] ?? 0),
                'period_start' => date('d/m/Y', $item['periodStartTime'] ?? time()),
                'period_end' => date('d/m/Y', $item['periodEndTime'] ?? time()),
                'seller_comm_ratio' => $item['sellerCommCoveRatio'] ?? '0',
            ];
        }, $results['data']['shopOfferV2']['nodes']);
    }

    public function formatBudget($budget)
    {
        $budgetLabels = [
            0 => 'Ilimitado',
            3 => 'Normal (Acima de 50%)',
            2 => 'Baixo (Abaixo de 50%)',
            1 => 'Muito Baixo (Abaixo de 30%)'
        ];
        return $budgetLabels[$budget] ?? 'Desconhecido';
    }

    public function normalizeProductOffers($results)
    {
        if (!isset($results['data']['productOfferV2']['nodes'])) {
            return [];
        }

        return array_map(function ($item) {
            $existyItem = ProductAffiliateLink::where('api_id', $item['itemId'])->exists();
            return [
                'id' => $item['itemId'] ?? null,
                'name' => $item['productName'] ?? 'Sem nome',
                'image' => $item['imageUrl'] ?? '',
                'price_min' => $item['priceMin'] ?? '0.00',
                'price_max' => $item['priceMax'] ?? '0.00',
                'commission' => $item['commissionRate'] ?? '0',
                'sales' => $item['sales'] ?? '0',
                'ratingStar' => $item['ratingStar'] ?? '0',
                'product_link' => $item['productLink'] ?? '',
                'offer_link' => $item['offerLink'] ?? '',
                'categories' => $item['productCatIds'] ?? [],
                'existyItem' => $existyItem,
            ];
        }, $results['data']['productOfferV2']['nodes']);
    }


    // SELLERs
    private function generateSellerSignature($path, $timestamp, $accessToken, $shopId)
    {
        $baseString = $this->appId . $path . $timestamp . $accessToken . $shopId . $this->secret;
        return hash_hmac('sha256', $baseString, $this->secret);
    }

    public function sendRequestSeller($endpoint, $params = [])
    {
        $settings = $this->getSettings();
        $timestamp = time();
        $parter_id = $settings['parter_id'];
        $shopId = $settings['shop_id'];
        $accessToken = $settings['parter_key'];

        $sign = $this->generateSellerSignature($endpoint, $timestamp, $accessToken, $shopId);

        $queryParams = [
            'partner_id' => $parter_id,
            'timestamp'  => $timestamp,
            'access_token' => $accessToken,
            'shop_id'    => $shopId,
            'sign'       => $sign
        ];

        try {
            $response = $this->client->request('GET', $this->baseUriSeller . $endpoint, [
                'query' => array_merge($queryParams, $params)
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

    public function getShopeeCategories($language = 'en')
    {
        $endpoint = '/product/get_category';
        $params = [
            'language' => $language
        ];

        return $this->sendRequestSeller($endpoint, $params);
    }


    private function handleClientException(ClientException $e)
    {
        $responseBody = json_decode($e->getResponse()->getBody()->getContents(), true);

        return [
            'type' => 'ClientException',
            'error' => true,
            'message' => $this->translateShopeeError($responseBody),
            'original' => $responseBody
        ];
    }

    private function handleRequestException(RequestException $e)
    {
        return [
            'type' => 'RequestException',
            'error' => true,
            'message' => $e->getMessage(),
        ];
    }

    private function handleGeneralException(\Exception $e)
    {
        return [
            'type' => 'GeneralException',
            'error' => true,
            'message' => $e->getMessage(),
        ];
    }

    private function translateShopeeError($responseBody)
    {
        if (!isset($responseBody['errors']) || !is_array($responseBody['errors'])) {
            return 'Erro desconhecido';
        }

        $error = $responseBody['errors'][0] ?? [];
        $code = $error['extensions']['code'] ?? null;
        $message = $error['extensions']['message'] ?? 'Erro desconhecido';

        $translations = [
            10000 => "Erro interno da Shopee. Tente novamente mais tarde.",
            10010 => "Erro ao processar a requisição. Verifique a sintaxe da query ou os tipos dos campos.",
            10020 => "Erro de autenticação. Verifique sua assinatura (AppID, Secret Key e Timestamp).",
            10030 => "Limite de requisições atingido. Aguarde e tente novamente.",
            11000 => "Erro de processamento da API. Pode ser um problema de configuração dos parâmetros enviados.",
        ];

        return $translations[$code] ?? "Erro desconhecido ({$message})";
    }
}
