<?php

namespace App\Services;

use App\Models\UserService;
use Carbon\Carbon;

class UserServiceService
{
	public function getAllUserServices($userId, $filters = [], $paginate = false, $perPage = 10) 
	{
		$query = UserService::where('user_id', $userId);

		if (!empty($filters['service_name'])) {
			$query->whereHas('service', function($q) use ($filters) {
                $q->where('title', 'LIKE', '%' . $filters['service_name'] . '%');
            });
		}

		if (!empty($filters['status'])) {
			$query->where('status', $filters['status']);
		}

		if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
			$query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
		}

        // Incluir relacionamentos
        $query->with(['service', 'statusRelation']);

		if ($paginate) {
			return $query->paginate($perPage);
		}

		return $query->get(); 
	}

	public function getUserServiceById($id) 
	{
		return UserService::with(['service', 'statusRelation'])->findOrFail($id);
	}

	public function createUserService($data) 
	{
        // Se a data de término não for informada e o período não for pagamento único,
        // calculamos automaticamente baseado no período
        if (empty($data['end_date']) && $data['period'] !== 'once') {
            $startDate = Carbon::parse($data['start_date']);
            
            switch ($data['period']) {
                case 'monthly':
                    $data['end_date'] = $startDate->copy()->addMonth()->format('Y-m-d');
                    break;
                case 'quarterly':
                    $data['end_date'] = $startDate->copy()->addMonths(3)->format('Y-m-d');
                    break;
                case 'semiannual':
                    $data['end_date'] = $startDate->copy()->addMonths(6)->format('Y-m-d');
                    break;
                case 'annual':
                    $data['end_date'] = $startDate->copy()->addYear()->format('Y-m-d');
                    break;
                case 'biennial':
                    $data['end_date'] = $startDate->copy()->addYears(2)->format('Y-m-d');
                    break;
            }
        }
        
        // Se for metadata, garantimos que é JSON
        if (isset($data['metadata']) && is_array($data['metadata'])) {
            $data['metadata'] = json_encode($data['metadata']);
        }                

        $data['start_date'] = Carbon::createFromFormat('d/m/Y', $data['start_date'])->format('Y-m-d');
        if(isset($data['end_date'])) {
            $data['end_date'] = Carbon::createFromFormat('d/m/Y', $data['end_date'])->format('Y-m-d');
        }
        
		return UserService::create($data);
	}

	public function updateUserService($id, $data) 
	{
		$model = UserService::findOrFail($id);
		
        // Se a data de término não for informada e o período não for pagamento único,
        // calculamos automaticamente baseado no período
        if (empty($data['end_date']) && $data['period'] !== 'once') {
            $startDate = Carbon::parse($data['start_date']);
            
            switch ($data['period']) {
                case 'monthly':
                    $data['end_date'] = $startDate->copy()->addMonth()->format('Y-m-d');
                    break;
                case 'quarterly':
                    $data['end_date'] = $startDate->copy()->addMonths(3)->format('Y-m-d');
                    break;
                case 'semiannual':
                    $data['end_date'] = $startDate->copy()->addMonths(6)->format('Y-m-d');
                    break;
                case 'annual':
                    $data['end_date'] = $startDate->copy()->addYear()->format('Y-m-d');
                    break;
                case 'biennial':
                    $data['end_date'] = $startDate->copy()->addYears(2)->format('Y-m-d');
                    break;
            }
        }
        
        // Se for metadata, garantimos que é JSON
        if (isset($data['metadata']) && is_array($data['metadata'])) {
            $data['metadata'] = json_encode($data['metadata']);
        }

        $data['start_date'] = Carbon::createFromFormat('d/m/Y', $data['start_date'])->format('Y-m-d');
        if(isset($data['end_date'])) {
            $data['end_date'] = Carbon::createFromFormat('d/m/Y', $data['end_date'])->format('Y-m-d');
        }
        
		$model->update($data);
		return $model;
	}

	public function deleteUserService($id) 
	{
		$model = UserService::findOrFail($id);
		return $model->delete();
	}

	public function getAllServicesFromAllUsers($filters = [], $perPage = 10)
	{
		$query = UserService::query();

		if (!empty($filters['service_name'])) {
			$query->whereHas('service', function($q) use ($filters) {
                $q->where('title', 'LIKE', '%' . $filters['service_name'] . '%');
            });
		}

		if (!empty($filters['status'])) {
			$query->where('status', $filters['status']);
		}

		if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
			$query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
		}

        // Incluir relacionamentos
        $query->with(['service', 'statusRelation', 'user']);

		return $query->paginate($perPage);
	}

} 