<?php

namespace App\Repositories\SQL;

use App\Models\TechnicalSupport;
use App\Repositories\Contracts\TechnicalSupportContract;
use Illuminate\Database\Eloquent\Collection;

class TechnicalSupportRepository extends BaseRepository implements TechnicalSupportContract
{
    /**
     * StaticPageRepository constructor.
     * @param TechnicalSupport $model
     */
    public function __construct(TechnicalSupport $model)
    {
        parent::__construct($model);
    }


    public function createForDoctor(array $data, int $doctorId): TechnicalSupport
    {
        try {
            return $this->model->create(array_merge($data, ['doctor_id' => $doctorId]));
        } catch (\Exception $e) {
            \Log::error('Failed to create technical support for doctor: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createForUser(array $data, int $userId): TechnicalSupport
    {
        try {
            return $this->model->create(array_merge($data, ['user_id' => $userId]));
        } catch (\Exception $e) {
            \Log::error('Failed to create technical support for user: ' . $e->getMessage());
            throw $e;
        }
    }

}