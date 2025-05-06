<?php

namespace App\Services;

use App\Models\Permission;

class PermissionService
{
	public function getAllPermissions($filters = []) 
	{
		$query = Permission::query();

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

	public function getPermissionById($id) 
	{
		return Permission::findOrFail($id);
	}

	public function createPermission($data) 
	{
		return Permission::create($data);
	}

	public function updatePermission($id, $data) 
	{
		$model = Permission::findOrFail($id);
		$model->update($data);
		return $model;
	}

	public function deletePermission($id) 
	{
		$model = Permission::findOrFail($id);
		return $model->delete();
	}

}
