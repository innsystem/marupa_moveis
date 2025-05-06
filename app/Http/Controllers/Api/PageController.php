<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PageService;
use Illuminate\Http\Request;

class PageController extends Controller
{
    protected $pageService;

    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['name', 'status', 'start_date', 'end_date']);
        return response()->json($this->pageService->getAllPages($filters));
    }

    public function show($id)
    {
        return response()->json($this->pageService->getPageById($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate(array (
  'title' => 'required|unique:pages,title|string',
  'slug' => 'required|unique:pages,slug|string',
  'keywords' => 'string',
  'content' => 'required|string',
  'status' => 'required|string',
));
        return response()->json($this->pageService->createPage($data), 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate(array (
  'title' => 'required|unique:pages,title|string',
  'slug' => 'required|unique:pages,slug|string',
  'keywords' => 'string',
  'content' => 'required|string',
  'status' => 'required|string',
));
        return response()->json($this->pageService->updatePage($id, $data));
    }

    public function destroy($id)
    {
        $this->pageService->deletePage($id);
        return response()->json(['message' => 'Page deleted']);
    }
}
