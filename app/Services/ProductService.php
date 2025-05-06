<?php

namespace App\Services;

use App\Jobs\ProcessNotificationJob;
use App\Models\Integration;
use App\Models\IntegrationCategory;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Geometry\Factories\RectangleFactory;
use App\Models\Product;
use App\Models\ProductAffiliateLink;
use App\Models\ProductImageGenerate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductService
{
	public function getAllProducts($filters = [])
	{
		$query = Product::query();

		if (!empty($filters['name'])) {
			$query->where('name', 'LIKE', '%' . $filters['name'] . '%');
		}

		if (!empty($filters['status'])) {
			$query->where('status', $filters['status']);
		}

		if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
			$query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
		}

		// Ordenar pelos produtos mais recentes
		$query->orderBy('created_at', 'desc');

		// Limitar o número de registros por página
		$perPage = $filters['per_page'] ?? 10;

		return $query->paginate($perPage);
	}

	public function getProductById($id)
	{
		return Product::findOrFail($id);
	}

	public function createProduct($data)
	{
		return Product::create($data);
	}

	public function updateProduct($id, $data)
	{
		$model = Product::findOrFail($id);
		$model->update($data);
		return $model;
	}

	public function deleteProduct($id)
	{
		$model = Product::findOrFail($id);
		return $model->delete();
	}

	// Funcao responsável por pesquisar os produtos na base
	public function searchProducts($query, $limit = 5)
	{
		return Product::where('name', 'like', "%{$query}%")
			->with('affiliateLink') // Relacionamento para obter o link afiliado
			->limit($limit) // Limita a 10 resultados
			->get()
			->map(function ($product) {
				return [
					'id' => $product->id,
					'name' => $product->name,
					'price' => $product->price,
					'price_promotion' => $product->price_promotion,
					'images' => $product->images,
					'affiliate_link' => $product->affiliateLink->affiliate_link ?? '#',
					'categories' => $product->categories->pluck('name')->toArray(),
					'created_at' => $product->created_at->format('d/m/Y')
				];
			});
	}
	/**
	 * Retorna os produtos mais recentes
	 *
	 * @param int $limit Limite de produtos a serem retornados
	 * @return \Illuminate\Support\Collection
	 */
	public function getRecentProducts($limit = 5)
	{
		return Product::where('status', 1) // Somente produtos ativos
			->with(['affiliateLink', 'categories']) // Relacionamentos para obter o link afiliado e categorias
			->orderBy('created_at', 'desc') // Ordenados pelos mais recentes
			->limit($limit) // Limita ao número de resultados solicitados
			->get()
			->map(function ($product) {
				return [
					'id' => $product->id,
					'name' => $product->name,
					'price' => $product->price,
					'price_promotion' => $product->price_promotion,
					'images' => $product->images,
					'affiliate_link' => $product->affiliateLink->affiliate_link ?? '#',
					'categories' => $product->categories->pluck('name')->toArray(),
					'created_at' => $product->created_at->format('d/m/Y')
				];
			});
	}

	/**
	 * Retorna os produtos em promoção
	 *
	 * @param int $limit Limite de produtos a serem retornados
	 * @return \Illuminate\Support\Collection
	 */
	public function getPromotionalProducts($limit = 5)
	{
		return Product::where('status', 1) // Somente produtos ativos
			->whereNotNull('price_promotion') // Produtos com preço promocional
			->where('price_promotion', '>', 0) // Preço promocional maior que zero
			->where('price_promotion', '<', 'price') // Preço promocional menor que o preço normal
			->with(['affiliateLink', 'categories']) // Relacionamentos para obter o link afiliado e categorias
			->orderBy('created_at', 'desc') // Ordenados pelos mais recentes
			->limit($limit) // Limita ao número de resultados solicitados
			->get()
			->map(function ($product) {
				$discountPercentage = 0;
				if ($product->price > 0) {
					$discountPercentage = round((($product->price - $product->price_promotion) / $product->price) * 100);
				}

				return [
					'id' => $product->id,
					'name' => $product->name,
					'price' => $product->price,
					'price_promotion' => $product->price_promotion,
					'discount_percentage' => $discountPercentage,
					'images' => $product->images,
					'affiliate_link' => $product->affiliateLink->affiliate_link ?? '#',
					'categories' => $product->categories->pluck('name')->toArray(),
					'created_at' => $product->created_at->format('d/m/Y')
				];
			});
	}


	// Fuuncao responsável por cadastrar produto API Marketplaces
	public function processProductNow($result)
	{
		$integration = Integration::where('slug', $result['slug_integration'])->first();
		$getIntegrationCategory = IntegrationCategory::whereIn('api_category_id', $result['product_categories'])->get();

		// Cadastra o produto
		$existyProduct = Product::where('name', $result['product_name'])->first();
		if ($existyProduct) {
			$product = $existyProduct;
		} else {
			$product = new Product();
		}
		$product_name = str_replace('QUEIMA DE ESTOQUE!', '', $result['product_name']);
		$product->name = $product_name;
		$product->slug = Str::slug($product_name);
		$product->description = $result['product_description'] ?? null;
		$product->images = [$result['product_images'] ?? null];
		$product->price = $result['product_price_max'] ? $result['product_price_max'] : $result['product_price_min'] ?? 0;
		$product->price_promotion = $result['product_price_min'] ?? 0;
		$product->status = 1;
		$product->save();

		// cadastra o link do produto de afiliado relacionad a integração
		$existyProductAffiliateLink = ProductAffiliateLink::where('api_id', $result['product_id'])->first();
		if ($existyProductAffiliateLink) {
			$procut_affiliate_links = $existyProductAffiliateLink;
		} else {
			$procut_affiliate_links = new ProductAffiliateLink();
		}
		$procut_affiliate_links->product_id = $product->id;
		$procut_affiliate_links->integration_id = $integration->id;
		$procut_affiliate_links->affiliate_link = $result['product_link'];
		$procut_affiliate_links->api_id = $result['product_id'] ?? null;
		$procut_affiliate_links->save();

		// Associar o produto com as categorias usando sync()
		$product->categories()->sync($getIntegrationCategory->pluck('category_id')->toArray());

		// download e salva as imagens
		$this->downloadAndStoreImages($product->id);

		// // adiciona ao catalogo da loja na meta
		// $this->facebookCatalog($product->id);

		// Gera a imagem do produto para o Story e envia no WhatsApp
		$this->generateProductStory($product->id);

		// Publica produto no grupo de promoções
		$this->publishProductGroup($product->id);

		// // Publica a imagem do produto no Feed - Multi Social
		// $this->publishProductImage($product->id);

		return response()->json('Produto Cadastrado/Atualizado com Sucesso', 200);
	}


	// Funcao auxiliar para configurações dos templates usado na funcao generateProductStory()
	private function getTemplateStoryConfig($templateName)
	{
		$configs = [
			'template_modelo_shopee.png' => [
				'image_x' => 140,
				'image_y' => 0,
				'image_width' => 800,
				'image_height' => 800,
				'text_x' => 525,
				'text_y' => 1420,
				'text_width' => 800,
				'text_size' => 52,
				'text_color' => '#FFFFFF',
				'bg_color' => '#c92f17',
				'text_price_x' => 395,
				'text_price_y' => 1550,
				'text_price_width' => 500,
				'text_price_size' => 50,
				'text_price_color' => '#c92f17',
				'bg_price_color' => '#FFFFFF',
				'font' => public_path('/galerias/fonts/nyala.ttf'),
			],
			'template_modelo_shopee_2.png' => [
				'image_x' => 140,
				'image_y' => 0,
				'image_width' => 800,
				'image_height' => 800,
				'text_x' => 525,
				'text_y' => 1420,
				'text_width' => 800,
				'text_size' => 52,
				'text_color' => '#4c3018',
				'bg_color' => '#FFFFFF',
				'text_price_x' => 395,
				'text_price_y' => 1550,
				'text_price_width' => 500,
				'text_price_size' => 50,
				'text_price_color' => '#4c3018',
				'bg_price_color' => '#FFFFFF',
				'font' => public_path('/galerias/fonts/nyala.ttf'),
			],
			'template_modelo_1.png' => [
				'image_x' => 140,
				'image_y' => 0,
				'image_width' => 800,
				'image_height' => 800,
				'text_x' => 525,
				'text_y' => 1520,
				'text_width' => 800,
				'text_size' => 52,
				'text_color' => '#FFFFFF',
				'bg_color' => '#4c3018',
				'text_price_x' => 395,
				'text_price_y' => 1650,
				'text_price_width' => 500,
				'text_price_size' => 48,
				'text_price_color' => '#4c3018',
				'bg_price_color' => '#FFFFFF',
				'font' => public_path('/galerias/fonts/nyala.ttf'),
			],
			'template_modelo_2.png' => [
				'image_x' => 140,
				'image_y' => 0,
				'image_width' => 800,
				'image_height' => 800,
				'text_x' => 525,
				'text_y' => 1520,
				'text_width' => 800,
				'text_size' => 52,
				'text_color' => '#4c3018',
				'bg_color' => '#FFFFFF',
				'text_price_x' => 395,
				'text_price_y' => 1650,
				'text_price_width' => 500,
				'text_price_size' => 48,
				'text_price_color' => '#4c3018',
				'bg_price_color' => '#FFFFFF',
				'font' => public_path('/galerias/fonts/nyala.ttf'),
			],
		];

		return $configs[$templateName] ?? $configs['template_modelo_1.png']; // Retorna um padrão caso não exista
	}

	// Funcao responsável por gerar imagem do produto para Story e Enviar no WhatsApp com Link
	public function generateProductStory($product_id)
	{
		$product = Product::findOrFail($product_id);

		if (!is_array($product->images) || empty($product->images)) {
			return response()->json(['error' => 'O produto não tem imagens armazenadas'], 400);
		}

		$directory = "public/products/{$product->id}";

		// Verifica se a pasta já existe antes de criá-la
		if (!Storage::exists($directory)) {
			Storage::makeDirectory($directory);
		}

		// Converte para o caminho físico real no servidor
		$storagePath = storage_path("app/{$directory}");

		// Garante que a pasta foi realmente criada antes de aplicar permissões
		if (file_exists($storagePath)) {
			chmod($storagePath, 0775); // Permissão para leitura/escrita pelo proprietário e grupo
		}

		$imagePath = public_path($product->images[0]);

		if (!file_exists($imagePath)) {
			$this->downloadAndStoreImages($product->id);
			$product->refresh();
			$imagePath = public_path(str_replace('/storage', 'storage/app/public', $product->images[0]));

			if (!file_exists($imagePath)) {
				return response()->json(['error' => 'Falha ao baixar a imagem do produto'], 500);
			}
		}

		// Escolhe um template aleatório
		$templatePath = public_path('galerias/templates/story/');
		$templates = glob($templatePath . '*.png');
		$randomTemplate = basename($templates[array_rand($templates)]); // Obtém apenas o nome do arquivo

		// Obtém as configurações do template
		$config = $this->getTemplateStoryConfig($randomTemplate);

		$nameOutput = '/story_' . basename($imagePath);
		$outputPath = $directory . $nameOutput;

		$manager = ImageManager::gd();
		$background = $manager->read($templatePath . $randomTemplate);
		$overlay = $manager->read($imagePath);

		// Ajusta tamanho da imagem do produto
		$overlay->resize($config['image_width'], $config['image_height'], function ($constraint) {
			$constraint->aspectRatio();
			$constraint->upsize();
		});

		// Posiciona a imagem conforme o template
		$background->place($overlay, 'left', $config['image_x'], $config['image_y'], 100);

		// Adiciona título do produto
		$text = strlen($product->name) > 50 ? Str::words($product->name, 8, '...') : $product->name;
		$text_x = $config['text_x'];
		$text_y = $config['text_y'];
		$text_size = $config['text_size'];
		$text_width = $config['text_width']; // Largura do fundo
		$text_height = $text_size + 60; // Altura do fundo
		$bg_color = isset($config['bg_color']) ? $config['bg_color'] : '#000000';

		// Adiciona fundo retangular atrás do texto
		$background->drawRectangle($text_x - ($text_width / 2), $text_y - ($text_height / 1.25), function (RectangleFactory $rectangle) use ($text_width, $text_height, $bg_color) {
			$rectangle->size($text_width, $text_height); // Define tamanho do retângulo
			$rectangle->background($bg_color); // Define cor de fundo preta
		});

		$background->text($text, $text_x, $text_y, function ($font) use ($config) {
			$font->filename($config['font']);
			$font->size($config['text_size']);
			$font->color($config['text_color']);
			$font->align('center');
			$font->wrap(700);
		});
		// Formatar preços corretamente
		$price_min = number_format($product->price_promotion, 2, ',', '.');
		$price_max = number_format($product->price, 2, ',', '.');

		$text_price = "Promoção R$ {$price_min}!\n" .
			($product->price_promotion > $product->price
				? "💰 Promoção R$ {$price_min} ~ R$ {$price_max}!\n\n"
				: "");
		$text_price_x = $config['text_price_x'];
		$text_price_y = $config['text_price_y'];
		$text_price_size = $config['text_price_size'];
		$text_price_width = $config['text_price_width']; // Largura do fundo
		$text_price_height = $text_price_size + 30; // Altura do fundo
		$bg_price_color = isset($config['bg_price_color']) ? $config['bg_price_color'] : '#000000';

		// Adiciona fundo retangular atrás do text_priceo
		$background->drawRectangle($text_price_x - ($text_price_width / 2), $text_price_y - ($text_price_height / 0.88), function (RectangleFactory $rectangle) use ($text_price_width, $text_price_height, $bg_price_color) {
			$rectangle->size($text_price_width, $text_price_height); // Define tamanho do retângulo
			$rectangle->background($bg_price_color); // Define cor de fundo preta
		});

		// Adiciona o text_priceo por cima do fundo
		$background->text($text_price, $text_price_x, $text_price_y, function ($font) use ($config) {
			$font->filename($config['font']);
			$font->size($config['text_price_size']);
			$font->color($config['text_price_color']); // Cor do texto
			$font->align('center');
			$font->wrap(500);
		});

		// Salva a imagem gerada
		// $background->save($outputPath);

		// Salva a imagem no storage de forma pública
		Storage::put("public/products/{$product->id}{$nameOutput}", $background->encode());

		$url_image_created = asset('/storage/products/' . $product->id . '' . $nameOutput);
		$link_product = $product->getAffiliateLinkByIntegration('shopee');

		$notificationDataImage = [
			'image' => $url_image_created,
		];
		$notificationDataLink = [
			'link' => $link_product,
		];

		$numbers = [
			// '5516992747526',
			'120363398273653245@g.us', // Grupo de Story+link
		]; // Lista de números
		$randomNumber = $numbers[array_rand($numbers)]; // Escolhe um número aleatório

		dispatch(new ProcessNotificationJob('whatsapp', $randomNumber, 'General', 'whatsapp', 'product_send_image', $notificationDataImage));
		dispatch(new ProcessNotificationJob('whatsapp', $randomNumber, 'General', 'whatsapp', 'product_send_link', $notificationDataLink));

		return response()->json(['message' => 'Imagem gerada com sucesso!', 'link_affiliate' => $link_product, 'image' => $url_image_created]);
	}

	private function getTemplateFeedConfig($templateName)
	{
		$configs = [
			'template_modelo_1.png' => [
				'image_x' => 0,
				'image_y' => 0,
				'image_width' => 1080,
				'image_height' => 1080,
				'font' => public_path('/galerias/fonts/nyala.ttf'),
			],
			// 'template_modelo_2.png' => [
			// 	'image_x' => 0,
			// 	'image_y' => 0,
			// 	'image_width' => 1120,
			// 	'image_height' => 1120,
			// 	'text_x' => 500,
			// 	'text_y' => 1050,
			// 	'text_width' => 800,
			// 	'text_size' => 54,
			// 	'text_color' => '#4c3018', 
			// 	'bg_color' => '#FFFFFF', 
			// 	'text_price_x' => 395,
			// 	'text_price_y' => 1175,
			// 	'text_price_width' => 550,
			// 	'text_price_size' => 48,
			// 	'text_price_color' => '#FFFFFF', 
			// 	'bg_price_color' => '#4c3018', 
			// 	'font' => public_path('/galerias/fonts/nyala.ttf'),
			// ],
		];

		return $configs[$templateName] ?? $configs['template_modelo_1.png']; // Retorna um padrão caso não exista
	}

	// Funcao responsável por gerar imagem do produto para FEED no Instagram e Facebook
	public function publishProductImage($product_id)
	{
		$product = Product::find($product_id);

		if (!is_array($product->images) || empty($product->images)) {
			return response()->json(['error' => 'O produto não tem imagens armazenadas'], 400);
		}

		$directory = "public/products/{$product->id}";

		// Verifica se a pasta já existe antes de criá-la
		if (!Storage::exists($directory)) {
			Storage::makeDirectory($directory);
		}

		// Converte para o caminho físico real no servidor
		$storagePath = storage_path("app/{$directory}");

		// Garante que a pasta foi realmente criada antes de aplicar permissões
		if (file_exists($storagePath)) {
			chmod($storagePath, 0775); // Permissão para leitura/escrita pelo proprietário e grupo
		}

		$imagePath = public_path($product->images[0]);

		if (!file_exists($imagePath)) {
			$this->downloadAndStoreImages($product->id);
			$product->refresh();
			$imagePath = public_path(str_replace('/storage', 'storage/app/public', $product->images[0]));

			if (!file_exists($imagePath)) {
				return response()->json(['error' => 'Falha ao baixar a imagem do produto'], 500);
			}
		}

		// Escolhe um template aleatório
		$templatePath = public_path('galerias/templates/feed/');
		$templates = glob($templatePath . '*.png');
		$randomTemplate = basename($templates[array_rand($templates)]); // Obtém apenas o nome do arquivo

		// Obtém as configurações do template
		$config = $this->getTemplateFeedConfig($randomTemplate);

		$nameOutput = '/feed_' . basename($imagePath);
		$outputPath = $directory . $nameOutput;

		$manager = ImageManager::gd();
		$background = $manager->read($templatePath . $randomTemplate);
		$overlay = $manager->read($imagePath);

		// Ajusta tamanho da imagem do produto
		$overlay->resize($config['image_width'], $config['image_height'], function ($constraint) {
			$constraint->aspectRatio();
			$constraint->upsize();
		});

		// Posiciona a imagem conforme o template
		$background->place($overlay, 'left', $config['image_x'], $config['image_y'], 100);

		// Formatar preços corretamente
		$price_min = number_format($product->price_promotion, 2, ',', '.');
		$price_max = number_format($product->price, 2, ',', '.');

		/*
		// Adiciona título do produto
		$text = strlen($product->name) > 50 ? Str::words($product->name, 7, '...') : $product->name;
		$text_x = $config['text_x'];
		$text_y = $config['text_y'];
		$text_size = $config['text_size'];
		$text_width = $config['text_width']; // Largura do fundo
		$text_height = $text_size + 60; // Altura do fundo
		$bg_color = isset($config['bg_color']) ? $config['bg_color'] : '#000000';

		// Adiciona fundo retangular atrás do texto
		$background->drawRectangle($text_x - ($text_width / 2), $text_y - ($text_height / 1.25), function (RectangleFactory $rectangle) use ($text_width, $text_height, $bg_color) {
			$rectangle->size($text_width, $text_height); // Define tamanho do retângulo
			$rectangle->background($bg_color); // Define cor de fundo preta
		});

		// Adiciona o texto por cima do fundo
		$background->text($text, $text_x, $text_y, function ($font) use ($config) {
			$font->filename($config['font']);
			$font->size($config['text_size']);
			$font->color($config['text_color']); // Cor do texto
			$font->align('center');
			$font->wrap(700);
		});
				
		

		$text_price = "A partir de R$ {$price_min}!\n" .
		($product->price_promotion > $product->price
			? "💰 A partir de R$ {$price_min} ~ R$ {$price_max}!\n\n"
			: "");
		$text_price_x = $config['text_price_x'];
		$text_price_y = $config['text_price_y'];
		$text_price_size = $config['text_price_size'];
		$text_price_width = $config['text_price_width']; // Largura do fundo
		$text_price_height = $text_price_size + 30; // Altura do fundo
		$bg_price_color = isset($config['bg_price_color']) ? $config['bg_price_color'] : '#000000';

		// Adiciona fundo retangular atrás do text_priceo
		$background->drawRectangle($text_price_x - ($text_price_width / 2), $text_price_y - ($text_price_height / 0.88), function (RectangleFactory $rectangle) use ($text_price_width, $text_price_height, $bg_price_color) {
			$rectangle->size($text_price_width, $text_price_height); // Define tamanho do retângulo
			$rectangle->background($bg_price_color); // Define cor de fundo preta
		});

		// Adiciona o text_priceo por cima do fundo
		$background->text($text_price, $text_price_x, $text_price_y, function ($font) use ($config) {
			$font->filename($config['font']);
			$font->size($config['text_price_size']);
			$font->color($config['text_price_color']); // Cor do texto
			$font->align('center');
			$font->wrap(500);
		});

		*/

		// Salva a imagem gerada
		// $background->save($outputPath);

		// Salva a imagem no storage de forma pública
		Storage::put("public/products/{$product->id}{$nameOutput}", $background->encode());

		$url_image_created = asset('/storage/products/' . $product->id . '' . $nameOutput);

		$social_image = asset($url_image_created);

		// \Log::info('Social Image: ' . $social_image);

		// dd($social_image);

		// Criar hashtags baseadas no nome do produto (limitadas a 6)
		$productNameWords = explode(' ', $product->name);
		$hashtags = array_slice(array_map(fn($word) => '#' . preg_replace('/[^A-Za-z0-9]/', '', ucfirst($word)), $productNameWords), 0, 8);

		// Hashtags fixas
		$fixedHashtags = ['#decoris', '#house', '#shopee', '#ofertas', '#promocoes', '#descontos', '#achadinhos'];

		// Combinar todas as hashtags
		$allHashtags = implode(' ', array_merge($hashtags, $fixedHashtags));

		// Criar conteúdo para redes sociais
		$content = "🛍️ {$product->name} \n\n💰 A partir de R$ {$price_min}!\n" .
			($product->price_promotion > $product->price
				? "💰 A partir de R$ {$price_min} ~ R$ {$price_max}!\n\n"
				: "") .
			"📲 Link Direto ➡️ {$product->affiliateLink->affiliate_link}\n" .
			"📲 Site Completo ➡️ decorishouse.com.br\n" .
			"🔥 Story 24 horas com promoções\n" .
			"\n\n" .
			"{$allHashtags}";


		$baseUrl = "https://multisocial.chat/api/facebook";

		$queryParams = [
			'token'             => 'm7ThIZbEzdquOsY57IAvoSS6k1ZTdrLZ1u760QZuUF13gHfOLHGA5YWH0dtqccCT',
			'facebook_meta_id'  => 61,
			'name'              => $product->name,
			'content'           => $content,
			'media'             => $social_image,
			'local'             => ['instagram_post', 'facebook_post'],
			'mark_product'      => 0,
			'catalog_id'        => '',
			'retailer_id'       => $product_id,

		];

		// Constrói a URL com query strings automaticamente
		$urlWithParams = $baseUrl . '?' . http_build_query($queryParams);

		// \Log::info('UrlParams: ' . $urlWithParams);

		// Fazer a requisição
		$response = Http::post($urlWithParams);

		// \Log::info('Response:' . json_encode($response->body()));
		//dd($response->body());

		if ($response->successful()) {
			return ['title' => 'Postagem publicada com sucesso!', 'status' => 200];
		}

		if (!$response->successful()) {
			Log::info('badRequest:' . $response->body());

			return ['title' => $response->body(), 'status' => 422];
		}

		// Verificar se a requisição foi bem-sucedida
		if ($response->failed()) {
			Log::info('Response:' . json_encode($response->body()));
			return ['title' => 'Erro ao postar nas redes sociais', 'status' => 422];
		}
	}

	// Funcao responsável por publicar o produto no Grupo de Promoções
	public function publishProductGroup($product_id)
	{
		$product = Product::find($product_id);
		$url_image_created = asset($product->images[0]);
		$link_product = $product->getAffiliateLinkByIntegration('shopee');

		Log::info('URL Image: ' . $url_image_created);

		$notificationDataImage = [
			'image' => $url_image_created,
		];

		$notificationDataLink = [
			'title' => $this->breakAfterWords($product->name),
			'price' => $product->price_promotion ? $product->price_promotion : $product->price,
			'link' => $link_product,
			'integration_name' => 'Shopee',
		];

		$numbers = [
			// '5516992747526',
			'120363391875660032@g.us', // Grupo de Promoções
		]; // Lista de números
		$randomNumber = $numbers[array_rand($numbers)]; // Escolhe um número aleatório

		dispatch(new ProcessNotificationJob('whatsapp', $randomNumber, 'General', 'whatsapp', 'product_send_image_group', $notificationDataImage));
		dispatch(new ProcessNotificationJob('whatsapp', $randomNumber, 'General', 'whatsapp', 'product_send_link_group', $notificationDataLink));

		return response()->json(['message' => 'Imagem gerada com sucesso!', 'link_affiliate' => $link_product, 'image' => $url_image_created]);
		
	}

	public function breakAfterWords($text, $limit = 8) {
		$words = explode(' ', $text);
		if (count($words) <= $limit) {
			return $text;
		}
	
		return implode(' ', array_slice($words, 0, $limit)) . "*\n*" . implode(' ', array_slice($words, $limit));
	}

	// Funcao responsável por sincronizar o produto com o catálogo do Facebook
	public function facebookCatalog($product_id)
	{
		$product_affiliate = ProductAffiliateLink::where('product_id', $product_id)->first();

		$catalogId = '1359397078637160';

		$accessToken = 'EAAHqUWZCqHiUBO4y4AayR6MZAGUL0bcENAFPA9gZCNq35cjlYwp0w6JwAaOkKMTyKf4QbFtdX3mMnulwIiZCCn1KaY0UQHqTgYNrYPycEjbnlSubipPZBy1aFrDFz7ScHs89IACUgXG7RG2iJ5fBHmqZBBfWoNCavtBN3VRZAOlZCuvLxZCRGvUwqhCjHZCZCqOhyQ7lM5ARRFBlZBPlbjSRcfZApe7NKawXFHcTo8gZDZD';

		$data = [
			'retailer_id' => $product_affiliate->product->id,
			'name' => $product_affiliate->product->name,
			'description' => $product_affiliate->product->name,
			'price' => round($product_affiliate->product->price * 100), // Convertendo para centavos
			'currency' => 'BRL',
			'availability' => 'in stock', // Exemplo: in stock, out of stock
			'condition' => 'new', // Exemplo: new, refurbished
			'image_url' => asset($product_affiliate->product->images[0]),
			'url' => $product_affiliate->affiliate_link,
		];

		//Log::info(json_encode($data));

		// Verificar se o produto já existe no catálogo
		$responseCheck = Http::withToken($accessToken)
			->get("https://graph.facebook.com/v22.0/" . $catalogId . "/products", [
				'filter' => '{"retailer_id":{"eq":"' . $product_id . '"}}',
			]);

		if ($responseCheck->failed()) {
			Log::error("Erro ao consultar o produto {$product_affiliate->product->id}: " . $responseCheck->body());
			return response()->json(['message' => 'Erro ao consultar o produto'], 400);
		}

		$existingProduct = $responseCheck->json();

		// Se encontrar o produto, tenta atualizar, caso contrário, cria um novo
		if (isset($existingProduct['data']) && count($existingProduct['data']) > 0) {
			$existingProductId = $existingProduct['data'][0]['id'];
			$responseUpdate = Http::withToken($accessToken)
				->post("https://graph.facebook.com/v22.0/{$existingProductId}", $data);


			if ($responseUpdate->failed()) {
				Log::error("Erro ao atualizar produto {$product_affiliate->product->id}: " . $responseUpdate->body());
				return response()->json(['message' => 'Erro ao atualizar produto'], 400);
			} else {
				Log::info("Produto {$product_affiliate->product->id} atualizado com sucesso.");
				return response()->json(['message' => 'Produto atualizado com sucesso'], 200);
			}
		} else {
			$responseCreate = Http::withToken($accessToken)
				->post("https://graph.facebook.com/v22.0/{$catalogId}/products", $data);

			if ($responseCreate->failed()) {
				Log::error("Erro ao criar produto {$product_affiliate->product->id}: " . $responseCreate->body());
				return response()->json(['message' => 'Erro ao criar produto'], 400);
			} else {
				Log::info("Produto {$product_affiliate->product->id} criado com sucesso.");
				return response()->json(['message' => 'Produto criado com sucesso'], 200);
			}
		}
	}

	//Sincroniza todos os produtos ou um lote específico com o catálogo do Facebook
	public function syncFacebookCatalog($batchSize = 10, $offset = 0)
	{
		// Busca os produtos ativos em lotes para evitar sobrecarga
		$products = Product::where('status', 1)
			->skip($offset)
			->take($batchSize)
			->get();

		$total = Product::where('status', 1)->count();
		$processed = 0;
		$success = 0;
		$failed = 0;

		foreach ($products as $product) {
			try {
				// Processa cada produto individualmente
				$this->facebookCatalog($product->id);
				$success++;

				// Adiciona um pequeno delay para evitar bloqueios por muitas requisições
				sleep(1);
			} catch (\Exception $e) {
				Log::error("Erro ao processar produto {$product->id} para catálogo Facebook: " . $e->getMessage());
				$failed++;
			}

			$processed++;
		}

		return [
			'total' => $total,
			'processed' => $processed,
			'success' => $success,
			'failed' => $failed,
			'remaining' => $total - ($offset + $processed),
			'offset_next' => $offset + $batchSize
		];
	}

	// Função responsável por baixar e atualizar as fotos de produtos quando está em URL Externa
	public function downloadAndStoreImages($product_id)
	{
		$product = Product::findOrFail($product_id);

		if (!is_array($product->images)) {
			return response()->json(['error' => 'Formato de imagem inválido'], 400);
		}

		$localImages = [];
		$directory = "public/products/{$product->id}";

		// Cria o diretório se ele não existir
		if (!Storage::exists($directory)) {
			Storage::makeDirectory($directory);
		}

		// Verifica se a pasta foi realmente criada antes de aplicar permissões
		$storagePath = storage_path("app/{$directory}");
		if (file_exists($storagePath)) {
			chmod($storagePath, 0775);
		}

		foreach ($product->images as $index => $imageUrl) {
			try {
				// Faz o download da imagem
				$response = Http::get($imageUrl);
				if ($response->successful()) {
					$extension = pathinfo($imageUrl, PATHINFO_EXTENSION) ?: 'jpg';
					$filename = "product_{$product->id}_{$index}." . $extension;
					$directoryPublic = str_replace('/public', '', $directory);
					$path = "{$directoryPublic}/{$filename}";

					// Salva a imagem localmente
					Storage::put($path, $response->body());

					// Adiciona ao array local
					$localImages[] = Storage::url($path);
				}
			} catch (\Exception $e) {
				continue; // Se houver erro em uma imagem, apenas pula para a próxima
			}
		}

		// Atualiza o campo images no banco de dados com os novos caminhos
		if (!empty($localImages)) {
			$product->update(['images' => $localImages]);
		}

		return response()->json(['message' => 'Imagens baixadas com sucesso!', 'images' => $localImages]);
	}
}
