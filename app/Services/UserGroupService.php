<?php

namespace App\Services;

use App\Models\UserGroup;

class UserGroupService
{
	public function getAllUserGroups($filters = [])
	{
		$query = UserGroup::query();

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

	public function getUserGroupById($id)
	{
		return UserGroup::findOrFail($id);
	}

	public function createUserGroup($data)
	{
		return UserGroup::create($data);
	}

	public function updateUserGroup($id, $data)
	{
		$model = UserGroup::findOrFail($id);
		$model->update($data);
		return $model;
	}

	public function deleteUserGroup($id)
	{
		$model = UserGroup::findOrFail($id);
		return $model->delete();
	}
}
