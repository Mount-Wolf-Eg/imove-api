<?php

namespace App\Repositories\SQL;


use App\Constants\FileConstants;
use App\Repositories\Contracts\FileContract;
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
            return $consultation->medicalEquipments()->where('is_active', 1)->with($relations)->get();
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve medical equipment for consultation: ' . $e->getMessage());
            return new Collection();
        }
    }



    public function syncRelations($model, $attributes)
    {
        if (isset($attributes['photo'])) {
            if (is_file($attributes['photo'])){
                $file = resolve(FileContract::class)->create(['file' => $attributes['photo'],
                    'type' => FileConstants::MEDICAL_EQUIPMENT_PHOTO->value]);
            }else{
                $file = resolve(FileContract::class)->find($attributes['photo']);
            }
            $model->photo()->save($file);
        }
        return $model;
    }

}
