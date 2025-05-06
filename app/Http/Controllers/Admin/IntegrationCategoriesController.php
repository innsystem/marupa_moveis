<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\Status;
use App\Models\Integration;
use App\Models\IntegrationCategory;
use Carbon\Carbon;

class IntegrationCategoriesController extends Controller
{
    public $name = 'Categoria de Integração';
    public $folder = 'admin.pages.integration_categories';

    public function index()
    {
        $integrations = Integration::where('status', 1)->get();
        return view($this->folder . '.index', compact('integrations'));
    }

    public function load(Request $request)
    {
        $query = [];
        $filters = $request->only(['name', 'status', 'integration_id', 'date_range']);

        if (!empty($filters['name'])) {
            $query['name'] = $filters['name'];
        }

        if (!empty($filters['status'])) {
            $query['status'] = $filters['status'];
        }

        if (!empty($filters['integration_id'])) {
            $query['integration_id'] = $filters['integration_id'];
        }

        if (!empty($filters['date_range'])) {
            [$startDate, $endDate] = explode(' até ', $filters['date_range']);
            $query['start_date'] = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
            $query['end_date'] = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        }

        $categoriesQuery = IntegrationCategory::with('integration');

        if (!empty($query['name'])) {
            $categoriesQuery->where('api_category_name', 'like', '%' . $query['name'] . '%');
        }

        if (!empty($query['integration_id'])) {
            $categoriesQuery->where('integration_id', $query['integration_id']);
        }

        if (!empty($query['start_date']) && !empty($query['end_date'])) {
            $categoriesQuery->whereBetween('created_at', [$query['start_date'] . ' 00:00:00', $query['end_date'] . ' 23:59:59']);
        }

        $results = $categoriesQuery->orderBy('id', 'desc')->get();

        return view($this->folder . '.index_load', compact('results'));
    }

    public function create()
    {
        $integrations = Integration::where('status', 1)->get();
        return view($this->folder . '.form', compact('integrations'));
    }

    public function store(Request $request)
    {
        $result = $request->all();

        $rules = array(
            'integration_id' => 'required',
            'api_category_id' => 'required',
            'api_category_name' => 'required',
        );
        $messages = array(
            'integration_id.required' => 'A integração é obrigatória',
            'api_category_id.required' => 'O ID da categoria é obrigatório',
            'api_category_name.required' => 'O nome da categoria é obrigatório',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $category = IntegrationCategory::create($result);

        return response()->json($this->name . ' adicionada com sucesso', 200);
    }

    public function edit($id)
    {
        $result = IntegrationCategory::findOrFail($id);
        $integrations = Integration::where('status', 1)->get();

        return view($this->folder . '.form', compact('result', 'integrations'));
    }

    public function update(Request $request, $id)
    {
        $result = $request->all();

        $rules = array(
            'integration_id' => 'required',
            'api_category_id' => 'required',
            'api_category_name' => 'required',
        );
        $messages = array(
            'integration_id.required' => 'A integração é obrigatória',
            'api_category_id.required' => 'O ID da categoria é obrigatório',
            'api_category_name.required' => 'O nome da categoria é obrigatório',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $category = IntegrationCategory::findOrFail($id);
        $category->update($result);

        return response()->json($this->name . ' atualizada com sucesso', 200);
    }

    public function delete($id)
    {
        $category = IntegrationCategory::findOrFail($id);

        // Excluir categorias relacionadas
        $relatedCategories = Category::where('name', $category->api_category_name)->get();
        foreach ($relatedCategories as $relatedCategory) {
            $relatedCategory->delete();
        }

        // Excluir a categoria principal
        $category->delete();

        return response()->json($this->name . ' e suas categorias relacionadas foram excluídas com sucesso', 200);
    }
} 