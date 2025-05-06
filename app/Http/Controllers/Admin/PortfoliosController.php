<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\Status;
use App\Models\Portfolio;
use Carbon\Carbon;
use App\Services\PortfolioService;

class PortfoliosController extends Controller
{
    public $name = 'Portfólio'; //  singular
    public $folder = 'admin.pages.portfolios';

    protected $portfolioService;

    public function __construct(PortfolioService $portfolioService)
    {
        $this->portfolioService = $portfolioService;
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

        $results = $this->portfolioService->getAllPortfolios($query, true, $perPage);

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
            'title' => 'required|unique:portfolios,title',
            'slug' => 'required|unique:portfolios,slug',
            'description' => 'nullable',
            'status' => 'required',
            'location' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
        );
        $messages = array(
            'title.required' => 'título é obrigatório',
            'title.unique' => 'título já existe',
            'slug.required' => 'url amigável é obrigatório',
            'slug.unique' => 'url amigável já existe',
            'description.nullable' => 'descrição pode ser nulo',
            'status.required' => 'status é obrigatório',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $portfolio = $this->portfolioService->createPortfolio($result);

        if (isset($portfolio) && isset($portfolio->id)) {
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                $thumbIndex = $request->input('thumb');

                foreach ($images as $index => $image) {
                    $path = $image->store('portfolios', 'public');
                    $portfolio->images()->create([
                        'image_path' => $path,
                        'featured' => $index == $thumbIndex,
                    ]);
                }
            }
        }

        return response()->json($this->name . ' adicionado com sucesso', 200);
    }

    public function edit($id)
    {
        $result = $this->portfolioService->getPortfolioById($id);
        $statuses = Status::default();

        return view($this->folder . '.form', compact('result', 'statuses'));
    }

    public function update(Request $request, $id)
    {
        $result = $request->all();

        $rules = array(
            'title'         => "unique:portfolios,title,$id,id",
            'slug'         => "unique:portfolios,slug,$id,id",
            'description' => 'nullable',
            'status' => 'required',
            'location' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
        );
        $messages = array(
            'title.required' => 'título é obrigatório',
            'title.unique' => 'título já existe',
            'slug.required' => 'url amigável é obrigatório',
            'slug.unique' => 'url amigável já existe',
            'description.nullable' => 'descrição pode ser nulo',
            'status.required' => 'status é obrigatório',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $portfolio = $this->portfolioService->updatePortfolio($id, $result);

        if (isset($portfolio) && isset($portfolio->id)) {
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                $thumbIndex = $request->input('thumb');

                foreach ($images as $index => $image) {
                    $path = $image->store('portfolios', 'public');
                    $portfolio->images()->create([
                        'image_path' => $path,
                        'featured' => $index == $thumbIndex,
                    ]);
                }
            }
        }

        return response()->json($this->name . ' atualizado com sucesso', 200);
    }

    public function delete($id)
    {
        $this->portfolioService->deletePortfolio($id);

        return response()->json($this->name . ' excluído com sucesso', 200);
    }

    public function deleteImage($image_id)
    {
        $this->portfolioService->deleteImagePortfolio($image_id);

        return response()->json('Imagem do ' . $this->name . ' excluído com sucesso', 200);
    }

    public function defineImageThumb(Request $request, $portfolio_id)
    {
        $image_id = $request->input('image_id');

        $portfolio = Portfolio::findOrFail($portfolio_id);

        // Certifique-se de que a imagem pertence ao portfólio
        $image = $portfolio->images()->where('id', $image_id)->firstOrFail();

        // Atualiza todas as imagens para `featured = 0`
        $portfolio->images()->update(['featured' => 0]);

        // Define a imagem selecionada como `featured = 1`
        $image->update(['featured' => 1]);

        return response()->json('Imagem destacada atualizada com sucesso!', 200);
    }
}
