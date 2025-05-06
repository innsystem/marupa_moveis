<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;

class TransactionService
{
	public function getAllTransactions($filters = [])
	{
		$query = Transaction::query();

		if (!empty($filters['description'])) {
			$query->where('description', 'LIKE', '%' . $filters['description'] . '%');
		}

		if (!empty($filters['invoice_id'])) {
			$query->where('invoice_id', $filters['invoice_id']);
		}

		if (!empty($filters['date_range'])) {
			$datas = explode(' até ', $filters['date_range']);

			if (count($datas) > 1) {
				$filters['start_date'] = Carbon::createFromFormat('d/m/Y', $datas[0])->format('Y-m-d H:i');
				$filters['end_date'] = Carbon::createFromFormat('d/m/Y', $datas[1])->format('Y-m-d') . ' 23:59:59';

				if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
					$query->whereBetween('date', [$filters['start_date'], $filters['end_date']]);
				}
			} else {
				$dateSearch = Carbon::createFromFormat('d/m/Y', $filters['date_range'])->format('Y-m-d');
				$query->whereBetween('date', [$dateSearch . ' 00:00:00', $dateSearch . ' 23:59:59']);
			}
		}

		// Transações filtradas
		$perPage = $filters['per_page'] ?? 10;
		$transactions = $query->orderBy('id', 'DESC')->paginate($perPage);

		// Cálculo dos valores SOMENTE da página atual
		$totalAmount = $transactions->sum('amount');
		$incomeAmount = $transactions->where('type', 'income')->sum('amount');
		$expenseAmount = $transactions->where('type', 'expense')->sum('amount');

		// Contagem das transações SOMENTE da página atual
		$totalTransactions = $transactions->count();
		$incomeTransactions = $transactions->where('type', 'income')->count();
		$expenseTransactions = $transactions->where('type', 'expense')->count();

		return compact('transactions', 'totalAmount', 'incomeAmount', 'expenseAmount', 'totalTransactions', 'incomeTransactions', 'expenseTransactions');
	}

	public function getTransactionById($id)
	{
		return Transaction::findOrFail($id);
	}

	public function createTransaction($data)
	{
		return Transaction::create($data);
	}

	public function updateTransaction($id, $data)
	{
		$model = Transaction::findOrFail($id);

		$model->update($data);
		return $model;
	}

	public function deleteTransaction($id)
	{
		$model = Transaction::findOrFail($id);
		return $model->delete();
	}
}
