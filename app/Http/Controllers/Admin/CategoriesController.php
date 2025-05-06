<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\Status;
use App\Models\Category;
use Carbon\Carbon;
use App\Services\CategoryService;

class CategoriesController extends Controller
{
    public $name = 'Categoria'; //  singular
    public $folder = 'admin.pages.categories';

    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        return view($this->folder . '.index');
    }

    public function load(Request $request)
    {
        $query = [];
        $filters = $request->only(['name', 'status', 'date_range']);

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

        $results = $this->categoryService->getAllCategoriesGroups($filters);

        return view($this->folder . '.index_load', compact('results'));
    }

    public function create()
    {
        $statuses = Status::default();
        $categories = $this->categoryService->getAllCategories();

        return view($this->folder . '.form', compact('statuses', 'categories'));
    }

    public function store(Request $request)
    {
        $result = $request->all();

        if ($request->input('parent_id') && $request->input('parent_slug')) {
            $result['slug'] = $request->input('parent_slug') . '-' . $result['slug'];
        }

        $rules = array(
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'status' => 'required',
        );
        $messages = array(
            'name.required' => 'name é obrigatório',
            'name.unique' => 'nome já existe',
            'slug.required' => 'slug é obrigatório',
            'slug.unique' => 'nome amigável já existe',
            'status.required' => 'status é obrigatório',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        if ($request->hasFile('thumb')) {
            $thumb = $request->file('thumb');
            $thumbPath = $thumb->store('categories', 'public');
            $result['thumb'] = $thumbPath;
        }

        $category = $this->categoryService->createCategory($result);

        return response()->json($this->name . ' adicionado com sucesso', 200);
    }

    public function edit($id)
    {
        $result = $this->categoryService->getCategoryById($id);
        $statuses = Status::default();
        $categories = $this->categoryService->getAllCategories();

        return view($this->folder . '.form', compact('result', 'statuses', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $result = $request->all();

        $rules = array(
            'name' => "required",
            'slug' => "required|unique:categories,slug,$id,id",
            'status' => 'required',
        );
        $messages = array(
            'name.required' => 'name é obrigatório',
            'name.unique' => 'name já está sendo utilizado',
            'slug.required' => 'slug é obrigatório',
            'slug.unique' => 'slug já está sendo utilizado',
            'status.required' => 'status é obrigatório',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        if ($request->hasFile('thumb')) {
            $thumb = $request->file('thumb');
            $thumbPath = $thumb->store('categories', 'public');
            $result['thumb'] = $thumbPath;
        }

        $category = $this->categoryService->updateCategory($id, $result);

        return response()->json($this->name . ' atualizado com sucesso', 200);
    }

    public function delete($id)
    {
        $this->categoryService->deleteCategory($id);

        return response()->json($this->name . ' excluído com sucesso', 200);
    }
}
