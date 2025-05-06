<?php

namespace App\Services;

use App\Models\Page;

class PageService
{
	public function getAllPages($filters = [], $paginate = false, $perPage = 10) 
	{
		$query = Page::query();

		if (!empty($filters['name'])) {
			$query->where('title', 'LIKE', '%' . $filters['name'] . '%');
		}

		if (!empty($filters['status'])) {
			$query->where('status', $filters['status']);
		}

		if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
			$query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
		}

		if ($paginate) {
			return $query->paginate($perPage);
		}

		return $query->get(); 
	}

	public function getPageById($id) 
	{
		return Page::findOrFail($id);
	}

	public function createPage($data) 
	{
		return Page::create($data);
	}

	public function updatePage($id, $data) 
	{
		$model = Page::findOrFail($id);
		$model->update($data);
		return $model;
	}

	public function deletePage($id) 
	{
		$model = Page::findOrFail($id);
		return $model->delete();
	}

}
