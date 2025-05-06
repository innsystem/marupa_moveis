<?php

namespace App\Services;

use App\Models\User;

class UserService
{
	public function getAllUsers($filters = [])
	{
		$query = User::query();

		if (!empty($filters['user_group_id'])) {
			if (is_array($filters['user_group_id'])) {
				$query->whereIn('user_group_id', $filters['user_group_id']);
			} else {
				$query->where('user_group_id', $filters['user_group_id']);
			}
		}

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

	public function getUserById($id)
	{
		return User::findOrFail($id);
	}

	public function createUser($data)
	{
		$data['password'] = bcrypt($data['password']);

		return User::create($data);
	}

	public function updateUser($id, $data)
	{
		$model = User::findOrFail($id);

		if (!empty($data['password'])) {
			$data['password'] = bcrypt($data['password']);
		} else {
			unset($data['password']);
		}

		$model->update($data);
		return $model;
	}

	public function deleteUser($id)
	{
		$model = User::findOrFail($id);
		return $model->delete();
	}

	public function getUserAddresses($userId)
	{
		$user = User::findOrFail($userId);
		return $user->addresses;
	}

	public function getUserAddressById($userId, $addressId)
	{
		$user = User::findOrFail($userId);
		return $user->addresses()->findOrFail($addressId);
	}

	public function addUserAddress($userId, $data)
	{
		$user = User::findOrFail($userId);
		return $user->addresses()->create($data);
	}

	public function updateUserAddress($userId, $addressId, $data)
	{
		$user = User::findOrFail($userId);
		$address = $user->addresses()->findOrFail($addressId);
		$address->update($data);
		return $address;
	}

	public function deleteUserAddress($userId, $addressId)
	{
		$user = User::findOrFail($userId);
		$address = $user->addresses()->findOrFail($addressId);
		return $address->delete();
	}
}
