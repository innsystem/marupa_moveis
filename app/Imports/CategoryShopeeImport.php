<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\IntegrationCategory;
use App\Models\CategoryMapping;
use App\Models\Integration;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CategoryShopeeImport implements ToModel, WithHeadingRow
{
    private $integration;
    private $integrationCategories;

    public function __construct()
    {
        $this->integration = Integration::where('slug', 'shopee')->first();
    }

    public function model(array $row)
    {
        $category_store = Category::find($row['id_da_categoria_loja']);

        if (!$category_store) {
            return null; // Retorna null para evitar erro do Maatwebsite\Excel
        }

        $name = 'Sem nome';

        if ($row['3_nivel_de_categoria'] !== '-' && !empty($row['3_nivel_de_categoria'])) {
            $name = $row['3_nivel_de_categoria'];
        } elseif ($row['2_nivel_de_categoria'] !== '-' && !empty($row['2_nivel_de_categoria'])) {
            $name = $row['2_nivel_de_categoria'];
        } elseif ($row['1_nivel_de_categoria'] !== '-' && !empty($row['1_nivel_de_categoria'])) {
            $name = $row['1_nivel_de_categoria'];
        }

        $slug = Str::slug($name);

        // Criar e salvar a categoria de integração
        $existyCategory = Category::where('slug', $slug)->first();
        if ($existyCategory) {
            $category = $existyCategory;
        } else {
            $category = new Category();
        }
        $category->name = $name;
        $category->slug = $slug;
        $category->parent_id = $category_store->id;
        $category->status = 1;

        try {
            $category->save();
        } catch (\Exception $e) {
            Log::error('CategoryShopeeImport :: model - Erro ao salvar Category: ' . $e->getMessage());
            return null; // Retorna null se houver erro
        }

        // Criar e salvar a categoria de integração
        $existyIntegrationCategory = IntegrationCategory::where('api_category_id', $row['id_da_categoria'])->first();
        if ($existyIntegrationCategory) {
            $integration_category = $existyIntegrationCategory;
        } else {
            $integration_category = new IntegrationCategory();
        }
        $integration_category->integration_id = $this->integration->id;
        $integration_category->category_id = $category->id;
        $integration_category->api_category_name = $name;
        $integration_category->api_category_id = $row['id_da_categoria'];

        try {
            $integration_category->save();
        } catch (\Exception $e) {
            Log::error('CategoryShopeeImport :: model - Erro ao salvar IntegrationCategory: ' . $e->getMessage());
            return null; // Retorna null se houver erro
        }

        // Criar e salvar o mapeamento da categoria
        $category_mapping = new CategoryMapping();
        $category_mapping->category_id = $category_store->id;
        $category_mapping->integration_category_id = $integration_category->id;

        try {
            $category_mapping->save();
        } catch (\Exception $e) {
            Log::error('CategoryShopeeImport :: model - Erro ao salvar CategoryMapping: ' . $e->getMessage());
        }

        return $integration_category; // Retorna um modelo válido
    }
}
