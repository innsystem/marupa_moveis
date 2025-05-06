<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SliderService;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    protected $sliderService;

    public function __construct(SliderService $sliderService)
    {
        $this->sliderService = $sliderService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['name', 'status', 'start_date', 'end_date']);
        return response()->json($this->sliderService->getAllSliders($filters));
    }

    public function show($id)
    {
        return response()->json($this->sliderService->getSliderById($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate(array (
  'title' => 'required|string',
  'href' => 'string',
  'target' => 'required|string',
  'image' => 'required|string',
  'status' => 'required|string',
));
        return response()->json($this->sliderService->createSlider($data), 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate(array (
  'title' => 'required|string',
  'href' => 'string',
  'target' => 'required|string',
  'image' => 'required|string',
  'status' => 'required|string',
));
        return response()->json($this->sliderService->updateSlider($id, $data));
    }

    public function destroy($id)
    {
        $this->sliderService->deleteSlider($id);
        return response()->json(['message' => 'Slider deleted']);
    }
}
