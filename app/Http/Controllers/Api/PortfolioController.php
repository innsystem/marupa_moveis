<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PortfolioService;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    protected $portfolioService;

    public function __construct(PortfolioService $portfolioService)
    {
        $this->portfolioService = $portfolioService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['name', 'status', 'start_date', 'end_date']);
        return response()->json($this->portfolioService->getAllPortfolios($filters));
    }

    public function show($id)
    {
        return response()->json($this->portfolioService->getPortfolioById($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate(array(
            'title' => 'required|unique:portfolios,title|string',
            'slug' => 'required|unique:portfolios,slug|string',
            'description' => 'string',
            'status' => 'required|string',
            'location' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
        ));
        return response()->json($this->portfolioService->createPortfolio($data), 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate(array(
            'title' => 'required|unique:portfolios,title|string',
            'slug' => 'required|unique:portfolios,slug|string',
            'description' => 'string',
            'status' => 'required|string',
            'location' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
        ));
        return response()->json($this->portfolioService->updatePortfolio($id, $data));
    }

    public function destroy($id)
    {
        $this->portfolioService->deletePortfolio($id);
        return response()->json(['message' => 'Portfolio deleted']);
    }
}
