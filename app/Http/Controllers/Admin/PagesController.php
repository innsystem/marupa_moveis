<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\Status;
use App\Models\Page;
use Carbon\Carbon;
use App\Services\PageService;

class PagesController extends Controller
{
    public $name = 'Página'; //  singular
    public $folder = 'admin.pages.pages';

    protected $pageService;

    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    public function index()
    {
        return view($this->folder . '.index');
    }

    public function load(Request $request)
    {
        $query = [];
        $filters = $request->only(['name', 'status', 'date_range']);
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

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

        $results = $this->pageService->getAllPages($query, true, $perPage);

        if ($request->ajax()) {
            return view($this->folder . '.index_load', compact('results'));
        }

        return view($this->folder . '.index_load', compact('results'));
    }

    public function create()
    {
        $statuses = Status::default();

        return view($this->folder . '.form', compact('statuses'));
    }

    public function store(Request $request)
    {
        $result = $request->all();

        $rules = array(
            'title' => 'required|unique:pages,title',
            'slug' => 'required|unique:pages,slug',
            'keywords' => 'nullable',
            'content' => 'required',
            'status' => 'required',
        );
        $messages = array(
            'title.required' => 'título da página é obrigatório',
            'title.unique' => 'título da página já existe',
            'slug.required' => 'url amigável é obrigatório',
            'slug.unique' => 'url amigável já existe',
            'keywords.nullable' => 'palavras chaves pode ser nulo',
            'content.required' => 'conteúdo da página é obrigatório',
            'status.required' => 'status é obrigatório',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $page = $this->pageService->createPage($result);

        return response()->json($this->name . ' adicionada com sucesso', 200);
    }

    public function edit($id)
    {
        $result = $this->pageService->getPageById($id);
        $statuses = Status::default();

        return view($this->folder . '.form', compact('result', 'statuses'));
    }

    public function update(Request $request, $id)
    {
        $result = $request->all();

        $rules = array(
            'title' => "required|unique:pages,title,$id,id",
            'slug' => "required|unique:pages,slug,$id,id",
            'keywords' => 'nullable',
            'content' => 'required',
            'status' => 'required',
        );
        $messages = array(
            'title.required' => 'título da página é obrigatório',
            'title.unique' => 'título da página já existe',
            'slug.required' => 'url amigável é obrigatório',
            'slug.unique' => 'url amigável já existe',
            'keywords.nullable' => 'palavras chaves pode ser nulo',
            'content.required' => 'conteúdo da página é obrigatório',
            'status.required' => 'status é obrigatório',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $page = $this->pageService->updatePage($id, $result);

        return response()->json($this->name . ' atualizada com sucesso', 200);
    }

    public function delete($id)
    {
        $this->pageService->deletePage($id);

        return response()->json($this->name . ' excluída com sucesso', 200);
    }
}
