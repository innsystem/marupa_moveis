<?php

namespace App\Services;

use App\Models\Portfolio;
use App\Models\PortfolioImage;

class PortfolioService
{
	public function getAllPortfolios($filters = [], $paginate = false, $perPage = 10)
	{
		$query = Portfolio::query();

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

	public function getPortfolioById($id)
	{
		return Portfolio::findOrFail($id);
	}

	public function createPortfolio($data)
	{
		return Portfolio::create($data);
	}

	public function updatePortfolio($id, $data)
	{
		$model = Portfolio::findOrFail($id);
		$model->update($data);
		return $model;
	}

	public function deletePortfolio($id)
	{
		$model = Portfolio::findOrFail($id);
		return $model->delete();
	}

	public function deleteImagePortfolio($image_id)
	{
		$model = PortfolioImage::findOrFail($image_id);

		// Verifica se a imagem atual é a destacada
		if ($model->featured == 1) {
			// Busca a próxima imagem que não seja a atual
			$portfolio_image_next = PortfolioImage::where('portfolio_id', $model->portfolio_id)
				->where('id', '!=', $model->id)
				->first();

			// Define a próxima imagem como destacada, se existir
			if ($portfolio_image_next) {
				$portfolio_image_next->featured = 1;
				$portfolio_image_next->save();
			}
		}

		// Após tratar o destaque, exclua a imagem atual
		return $model->delete();
	}
}
