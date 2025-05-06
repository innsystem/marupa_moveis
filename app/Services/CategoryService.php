<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
	public function getAllCategories($filters = [])
	{
		$query = Category::query();

		if (!empty($filters['name'])) {
			$query->where('title', 'LIKE', '%' . $filters['name'] . '%');
		}

		if (!empty($filters['status'])) {
			$query->where('status', $filters['status']);
		}

		if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
			$query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
		}

		return $query->get();
	}

	public function getAllCategoriesGroups($filters = [])
	{
		$query = Category::query();

		if (!empty($filters['name'])) {
			$query->where('title', 'LIKE', '%' . $filters['name'] . '%');
		}

		if (!empty($filters['status'])) {
			$query->where('status', $filters['status']);
		}

		if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
			$query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
		}

		$categories = $query->orderBy('parent_id')->orderBy('name')->get();

		// Agrupar por parent_id
		$groupedCategories = $categories->groupBy('parent_id');

		return $groupedCategories;
	}

	public function getCategoryById($id)
	{
		return Category::findOrFail($id);
	}

	public function getCategoryByName($name)
	{
		return Category::where('name', $name)->first();
	}

	public function createCategory($data)
	{
		return Category::create($data);
	}

	public function updateCategory($id, $data)
	{
		$model = Category::findOrFail($id);
		$model->update($data);
		return $model;
	}

	public function deleteCategory($id)
	{
		$model = Category::findOrFail($id);
		return $model->delete();
	}
}
