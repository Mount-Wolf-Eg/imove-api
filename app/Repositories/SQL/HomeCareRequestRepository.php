<?php

namespace App\Repositories\SQL;

use App\Models\HomeCareRequest;
use App\Repositories\Contracts\HomeCareRequestContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class HomeCareRequestRepository extends BaseRepository implements HomeCareRequestContract
{

    /**
     * HomeCareRequestRepository constructor.
     * @param HomeCareRequest $model
     */
    public function __construct(HomeCareRequest $model)
    {
        parent::__construct($model);
    }


    public function store(array $data): HomeCareRequest
    {
        try {
            return $this->create($data);
        } catch (\Exception $e) {
            \Log::error('Failed to create home care request: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getAll(array $filters = [], array $relations = []): LengthAwarePaginator
    {
        try {
            $query = $this->model->query()->where('patient_id', auth()->user()->patient->id);

            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (!empty($filters['city_id'])) {
                $query->where('city_id', $filters['city_id']);
            }

            if (!empty($filters['medical_speciality_id'])) {
                $query->where('medical_speciality_id', $filters['medical_speciality_id']);
            }

            $limit = $filters['limit'] ?? 10;
            $page = $filters['page'] ?? 1;


            return $query->with($relations)->orderBy('id', 'desc')->paginate($limit, ['*'], 'page', $page);
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve home care requests: ' . $e->getMessage());
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1, [
                'path' => request()->url(),
                'query' => request()->query()
            ]);
        }
    }

    public function findOne(int $id, array $relations = []): HomeCareRequest
    {
        try {
            return $this->model->where('patient_id', auth()->user()->patient->id)
                ->with($relations)
                ->findOrFail($id);
        } catch (\Exception $e) {
            \Log::error('Failed to find home care request: ' . $e->getMessage());
            throw $e;
        }
    }

}
