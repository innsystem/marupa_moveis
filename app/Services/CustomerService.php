<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPreference;

class CustomerService
{
	public function getAllCustomers($filters = [], $perPage = 10)
	{
		$query = User::query();

		// Cliente é um usuário com user_group_id = 3
		$query->where('user_group_id', 3);

		if (!empty($filters['name'])) {
			$query->where('name', 'LIKE', '%' . $filters['name'] . '%');
		}

		if (!empty($filters['status'])) {
			$query->where('status', $filters['status']);
		}

		if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
			$query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
		}

		$customers = $query->orderBy('id', 'DESC')->paginate($perPage);
		$totalCustomers = $query->count();

		return compact('customers', 'totalCustomers');
	}

	public function getCustomerById($id)
	{
		return User::where('user_group_id', 3)->findOrFail($id);
	}

	public function createCustomer($data)
	{
		$data['password'] = bcrypt($data['password']);
		$data['user_group_id'] = 3; // User Group Id for Customers

		$user = User::create($data);
		
		// Criar preferências padrão se não existirem
		if (isset($data['payment_default'])) {
			$this->updateOrCreatePreferences($user->id, ['payment_default' => $data['payment_default']]);
		} else {
			$this->updateOrCreatePreferences($user->id, ['payment_default' => 'pix']);
		}

		return $user;
	}

	public function updateCustomer($id, $data)
	{
		$model = User::where('user_group_id', 3)->findOrFail($id);

		if (!empty($data['password'])) {
			$data['password'] = bcrypt($data['password']);
		} else {
			unset($data['password']);
		}

		// Atualizar preferências do usuário se fornecidas
		if (isset($data['payment_default'])) {
			$this->updateOrCreatePreferences($id, ['payment_default' => $data['payment_default']]);
			unset($data['payment_default']);
		}

		$model->update($data);
		return $model;
	}

	public function deleteCustomer($id)
	{
		$model = User::where('user_group_id', 3)->findOrFail($id);
		return $model->delete();
	}

	public function getCustomerAddresses($customerId)
	{
		$customer = User::where('user_group_id', 3)->findOrFail($customerId);
		return $customer->addresses;
	}

	public function getCustomerAddressById($customerId, $addressId)
	{
		$customer = User::where('user_group_id', 3)->findOrFail($customerId);
		return $customer->addresses()->findOrFail($addressId);
	}

	public function addCustomerAddress($customerId, $data)
	{
		$customer = User::where('user_group_id', 3)->findOrFail($customerId);
		return $customer->addresses()->create($data);
	}

	public function updateCustomerAddress($customerId, $addressId, $data)
	{
		$customer = User::where('user_group_id', 3)->findOrFail($customerId);
		$address = $customer->addresses()->findOrFail($addressId);
		$address->update($data);
		return $address;
	}

	public function deleteCustomerAddress($customerId, $addressId)
	{
		$customer = User::where('user_group_id', 3)->findOrFail($customerId);
		$address = $customer->addresses()->findOrFail($addressId);
		return $address->delete();
	}
	
	// Método para obter as preferências do usuário
	public function getCustomerPreferences($customerId)
	{
		$customer = User::where('user_group_id', 3)->findOrFail($customerId);
		return $customer->preferences ?? null;
	}
	
	// Método para atualizar ou criar preferências do usuário
	public function updateOrCreatePreferences($customerId, $data)
	{
		return UserPreference::updateOrCreate(
			['user_id' => $customerId],
			$data
		);
	}
}
