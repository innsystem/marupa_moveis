<?php

namespace App\Services;

use App\Models\Integration;
use Illuminate\Support\Facades\Cache;

class IntegrationService
{
	public function getAllIntegrations($filters = [])
	{
		$cacheKey = 'integrations'; // Gera uma chave única para cada conjunto de filtros


		// Verifica o cache antes de consultar o banco de dados
		return Cache::remember($cacheKey, 3600, function () use ($filters) {
			$query = Integration::query();

			// Aplicação dos filtros
			if (!empty($filters['name'])) {
				$query->where('title', 'LIKE', '%' . $filters['name'] . '%');
			}

			if (!empty($filters['status'])) {
				$query->where('status', $filters['status']);
			}

			if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
				$query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
			}

			// Retorna o resultado da consulta
			return $query->get();
		});
	}

	public function getAllIntegrationsGroupedByType($filters = [])
	{
		$query = Integration::query();

		// Aplicação dos filtros
		if (!empty($filters['name'])) {
			$query->where('name', 'LIKE', '%' . $filters['name'] . '%');
		}

		if (!empty($filters['status'])) {
			$query->where('status', $filters['status']);
		}

		if (!empty($filters['type'])) {
			$query->where('type', $filters['type']);
		}

		// Obter resultados agrupados por 'type' e ordenados alfabeticamente
		return $query->orderBy('type')->orderBy('name')->get()->groupBy('type');
	}

	public function getIntegrationById($id)
	{
		return Integration::findOrFail($id);
	}

	public function getIntegrationBySlug($slug)
	{
		return Integration::where('slug', $slug)->first();
	}

	public function createIntegration($data)
	{
		return Integration::create($data);
	}

	public function updateIntegration($id, $data)
	{
		$model = Integration::findOrFail($id);
		$model->update($data);
		return $model;
	}

	public function deleteIntegration($id)
	{
		$model = Integration::findOrFail($id);
		return $model->delete();
	}
}
