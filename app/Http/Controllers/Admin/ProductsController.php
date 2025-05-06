<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Integration;
use App\Models\Product;
use App\Models\ProductAffiliateLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Status;
use App\Services\CategoryService;
use App\Services\ProductService;
use Carbon\Carbon;

class ProductsController extends Controller
{
    public $name = 'Produto'; //  singular
    public $folder = 'admin.pages.products';

    protected $productService;
    protected $categoryService;

    public function __construct(ProductService $productService, CategoryService $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $integrations = Integration::where('type', 'marketplaces')->get();

        return view($this->folder . '.index', compact('integrations'));
    }

    public function load(Request $request)
    {
        $query = [];
        $filters = $request->only(['name', 'status', 'date_range', 'per_page']);

        if (!empty($filters['name'])) {
            $query['name'] = $filters['name'];
        }

        if (!empty($filters['status'])) {
            $query['status'] = $filters['status'];
        }

        if (!empty($filters['date_range'])) {
            [$startDate, $endDate] = explode(' até ', $filters['date_range']);
            $query['start_date'] = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
            $query['end_date'] = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        }

        // Define o número de produtos por página
        $query['per_page'] = $filters['per_page'] ?? 10;

        $products = $this->productService->getAllProducts($query);

        return view($this->folder . '.index_load', compact('products'));
    }

    public function create()
    {
        $statuses = Status::default();
        $integrations = Integration::where('type', 'marketplaces')->get();

        return view($this->folder . '.form', compact('statuses', 'integrations'));
    }

    public function store(Request $request)
    {
        $result = $request->all();

        $rules = array(
            'name' => 'required|unique:products,name',
            'slug' => 'required|unique:products,slug',
            'images' => 'required',
            'price' => 'required',
            'status' => 'required',
        );
        $messages = array(
            'name.required' => 'name é obrigatório',
            'name.unique' => 'nome já existe',
            'slug.required' => 'url amigável é obrigatório',
            'slug.unique' => 'nome amigável já existe',
            'images.required' => 'images é obrigatório',
            'price.required' => 'price é obrigatório',
            'status.required' => 'status é obrigatório',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        // Convertendo imagens (separadas por vírgula) para JSON
        $result['images'] = array_map('trim', explode(',', $result['images']));

        // Convertendo preço (removendo caracteres indesejados como vírgula no formato brasileiro)
        $result['price'] = floatval(str_replace(',', '.', $result['price']));
        $result['price_promotion'] = isset($result['price_promotion']) ? floatval(str_replace(',', '.', $result['price_promotion'])) : null;

        $product = $this->productService->createProduct($result);

        // Relacionando categorias (Many-to-Many)
        $product->categories()->sync($result['categories']);

        if (!empty($result['marketplace']) && !empty($result['affiliate_links'])) {
            foreach ($result['marketplace'] as $index => $marketplaceId) {
                if (!empty($marketplaceId) && !empty($result['affiliate_links'][$index])) {
                    $product->affiliateLinks()->create([
                        'integration_id' => $marketplaceId,
                        'affiliate_link' => $result['affiliate_links'][$index],
                    ]);
                }
            }
        }

        return response()->json($this->name . ' adicionado com sucesso', 200);
    }

    public function edit($id)
    {
        $result = $this->productService->getProductById($id);
        $statuses = Status::default();
        $integrations = Integration::where('type', 'marketplaces')->get();

        return view($this->folder . '.form', compact('result', 'statuses', 'integrations'));
    }

    public function update(Request $request, $id)
    {
        $result = $request->all();

        $rules = array(
            'name' => "required|unique:products,name,$id,id",
            'slug' => "required|unique:products,slug,$id,id",
            'images' => 'required',
            'price' => 'required',
            'status' => 'required',
        );
        $messages = array(
            'name.required' => 'name é obrigatório',
            'name.unique' => 'name já está sendo utilizado',
            'slug.required' => 'slug é obrigatório',
            'slug.unique' => 'slug já está sendo utilizado',
            'images.required' => 'images é obrigatório',
            'price.required' => 'price é obrigatório',
            'status.required' => 'status é obrigatório',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        // Convertendo imagens (separadas por vírgula) para JSON
        $result['images'] = array_map('trim', explode(',', $result['images']));

        // Convertendo preço (removendo caracteres indesejados como vírgula no formato brasileiro)
        $result['price'] = floatval(str_replace(',', '.', $result['price']));
        $result['price_promotion'] = isset($result['price_promotion']) ? floatval(str_replace(',', '.', $result['price_promotion'])) : null;

        $product = $this->productService->updateProduct($id, $result);

        // Relacionando categorias (Many-to-Many)
        $product->categories()->sync($result['categories']);

        $product->affiliateLinks()->delete();

        if (!empty($result['marketplace']) && !empty($result['affiliate_links'])) {
            foreach ($result['marketplace'] as $index => $marketplaceId) {
                // Verifique se tanto o marketplaceId quanto o link são válidos
                if (!empty($marketplaceId) && !empty($result['affiliate_links'][$index])) {
                    $product->affiliateLinks()->create([
                        'integration_id' => $marketplaceId,
                        'affiliate_link' => $result['affiliate_links'][$index],
                    ]);
                }
            }
        }

        return response()->json($this->name . ' atualizado com sucesso', 200);
    }

    public function delete($id)
    {
        $this->productService->deleteProduct($id);

        return response()->json($this->name . ' excluído com sucesso', 200);
    }

    public function generateImageStory($id)
    {
        $result = $this->productService->generateProductStory($id);

        return $result;
    }

    public function generateImageFeed($id)
    {
        $result = $this->productService->publishProductImage($id);

        return response()->json($result['title'], $result['status']);
    }

    public function generateSuggestions()
    {
        // Busca uma categoria pai aleatória e carrega as subcategorias e os produtos
        $category = Category::with(['products.affiliateLinks', 'children.products'])->whereNull('parent_id')->inRandomOrder()->first();

        if (!$category) {
            return response()->json(['error' => 'Nenhuma categoria encontrada.'], 404);
        }

        $products = [];

        foreach ($category->children as $child) {
            $product = $child->randomProduct();

            if ($product) {
                $product_name = $product->name;
                $product_link = $product->getAffiliateLinkByIntegration('shopee') ?? '#';
                $products[] = [
                    'name' => $product_name,
                    'link' => $product_link,
                ];
            }
        }

        return response()->json([
            'category' => $category->name,
            'products' => $products
        ]);
    }

    public function facebookCatalog($id)
    {
        $result = $this->productService->facebookCatalog($id);

        return $result;
    }

    public function publishProductGroup($id)
    {
        $result = $this->productService->publishProductGroup($id);

        return $result;
    }
}