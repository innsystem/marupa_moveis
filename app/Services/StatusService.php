<?php

namespace App\Services;

use App\Models\Status;

class StatusService
{
	public function getAllStatuses($filters = []) 
	{
		$query = Status::query();

		if (!empty($filters['name'])) {
			$query->where('name', 'LIKE', '%' . $filters['name'] . '%');
		}

		if (!empty($filters['status'])) {
			$query->where('status', $filters['status']);
		}

		if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
			$query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
		}

		return $query->get(); 
	}

	public function getAllStatusesGroups($filters = []) 
	{
		$query = Status::query();

		if (!empty($filters['name'])) {
			$query->where('name', 'LIKE', '%' . $filters['name'] . '%');
		}

		if (!empty($filters['status'])) {
			$query->where('status', $filters['status']);
		}

		if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
			$query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
		}

		return $query->get()->groupBy('type'); 
	}

	public function getStatusById($id) 
	{
		return Status::findOrFail($id);
	}

	public function createStatus($data) 
	{
		return Status::create($data);
	}

	public function updateStatus($id, $data) 
	{
		$model = Status::findOrFail($id);
		$model->update($data);
		return $model;
	}

	public function deleteStatus($id) 
	{
		$model = Status::findOrFail($id);
		return $model->delete();
	}

}
