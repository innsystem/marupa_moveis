<?php

namespace App\Services;

use App\Models\BankAccount;

class BankAccountService
{
	public function getAllBankAccount($filters = []) 
	{
		$query = BankAccount::query();

		if (!empty($filters['name'])) {
			$query->where('bank_name', 'LIKE', '%' . $filters['name'] . '%');
		}

		if (!empty($filters['status'])) {
			$query->where('status', $filters['status']);
		}

		if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
			$query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
		}

		return $query->get(); 
	}

	public function getBankAccountById($id) 
	{
		return BankAccount::findOrFail($id);
	}

	public function createBankAccount($data) 
	{
		return BankAccount::create($data);
	}

	public function updateBankAccount($id, $data) 
	{
		$model = BankAccount::findOrFail($id);
		$model->update($data);
		return $model;
	}

	public function deleteBankAccount($id) 
	{
		$model = BankAccount::findOrFail($id);
		return $model->delete();
	}

}
