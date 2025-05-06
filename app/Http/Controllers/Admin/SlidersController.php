<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\Status;
use App\Models\Slider;
use Carbon\Carbon;
use App\Services\SliderService;

class SlidersController extends Controller
{
    public $name = 'Slider'; //  singular
    public $folder = 'admin.pages.sliders';

    protected $sliderService;

    public function __construct(SliderService $sliderService)
    {
        $this->sliderService = $sliderService;
    }

    public function index()
    {
        return view($this->folder . '.index');
    }

    public function load(Request $request)
    {
        $query = [];
        $filters = $request->only(['name', 'status', 'date_range', 'date_start', 'date_end']);
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
        if (!empty($filters['date_start'])) {
            $query['date_start'] = $filters['date_start'];
        }
        if (!empty($filters['date_end'])) {
            $query['date_end'] = $filters['date_end'];
        }

        $results = $this->sliderService->getAllSliders($query, true, $perPage);

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
            'title' => 'required',
            'image' => 'required',
            'status' => 'required',
        );
        $messages = array(
            'title.required' => 'title é obrigatório',
            'image.required' => 'image é obrigatório',
            'status.required' => 'status é obrigatório',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('sliders', 'public');
            $result['image'] = $imagePath;
        }

        $slider = $this->sliderService->createSlider($result);

        return response()->json($this->name . ' adicionado com sucesso', 200);
    }

    public function edit($id)
    {
        $result = $this->sliderService->getSliderById($id);
        $statuses = Status::default();

        return view($this->folder . '.form', compact('result', 'statuses'));
    }

    public function update(Request $request, $id)
    {
        $result = $request->all();

        // 'email'         => "unique:sliders,email,$id,id",
        $rules = array(
            'title' => 'required',
            'href' => 'nullable',
            'target' => 'required',
            'image' => 'required',
            'status' => 'required',
        );
        $messages = array(
            'title.required' => 'title é obrigatório',
            'href.required' => 'href é obrigatório',
            'href.nullable' => 'href pode ser nulo',
            'target.required' => 'target é obrigatório',
            'image.required' => 'image é obrigatório',
            'status.required' => 'status é obrigatório',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('sliders', 'public');
            $result['image'] = $imagePath;
        }
        $slider = $this->sliderService->updateSlider($id, $result);

        return response()->json($this->name . ' atualizado com sucesso', 200);
    }

    public function delete($id)
    {
        $this->sliderService->deleteSlider($id);

        return response()->json($this->name . ' excluído com sucesso', 200);
    }
}
