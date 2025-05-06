<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TestimonialService;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    protected $testimonialService;

    public function __construct(TestimonialService $testimonialService)
    {
        $this->testimonialService = $testimonialService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['name', 'status', 'start_date', 'end_date']);
        return response()->json($this->testimonialService->getAllTestimonials($filters));
    }

    public function show($id)
    {
        return response()->json($this->testimonialService->getTestimonialById($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate(array (
  'name' => 'required|string',
  'avatar' => 'required|string',
  'content' => 'required|string',
  'rating' => 'required|integer',
  'localization' => 'required|string',
  'sort_order' => 'required|integer',
  'status' => 'required|string',
));
        return response()->json($this->testimonialService->createTestimonial($data), 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate(array (
  'name' => 'required|string',
  'avatar' => 'required|string',
  'content' => 'required|string',
  'rating' => 'required|integer',
  'localization' => 'required|string',
  'sort_order' => 'required|integer',
  'status' => 'required|string',
));
        return response()->json($this->testimonialService->updateTestimonial($id, $data));
    }

    public function destroy($id)
    {
        $this->testimonialService->deleteTestimonial($id);
        return response()->json(['message' => 'Testimonial deleted']);
    }
}
