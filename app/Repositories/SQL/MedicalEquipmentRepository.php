<?php

namespace App\Repositories\SQL;

use App\Models\Consultation;
use App\Models\MedicalEquipment;
use App\Repositories\Contracts\MedicalEquipmentContract;
use Illuminate\Database\Eloquent\Collection;

class MedicalEquipmentRepository extends BaseRepository implements MedicalEquipmentContract
{
    /**
     * MedicalEquipmentRepository constructor.
     * @param MedicalEquipment $model
     */
    public function __construct(MedicalEquipment $model)
    {
        parent::__construct($model);
    }


    public function assignToConsultation(Consultation $consultation, array $medicalEquipmentIds, int $doctorId): bool
    {
        try {
            if ($consultation->doctor_id !== $doctorId) {
                return false;
            }
            foreach ($medicalEquipmentIds as $equipmentId) {
                $consultation->medicalEquipments()->syncWithoutDetaching([$equipmentId => ['doctor_id' => $doctorId]]);
            }
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to assign medical equipment: ' . $e->getMessage());
            return false;
        }
    }

    public function removeFromConsultation(Consultation $consultation, array $medicalEquipmentIds, int $doctorId): bool
    {
        try {
            if ($consultation->doctor_id !== $doctorId) {
                return false;
            }

            $consultation->medicalEquipments()->detach($medicalEquipmentIds);
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to remove medical equipment: ' . $e->getMessage());
            return false;
        }
    }
    
 
    public function getByConsultation(Consultation $consultation, array $relations = []): Collection
    {
        try {
            return $consultation->medicalEquipments()->with($relations)->get();
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve medical equipment for consultation: ' . $e->getMessage());
            return new Collection();
        }
    }
    
}
