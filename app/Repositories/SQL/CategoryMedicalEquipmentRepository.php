<?php

namespace App\Repositories\SQL;

use App\Models\CategoryMedicalEquipment;
use App\Repositories\Contracts\CategoryMedicalEquipmentContract;

class CategoryMedicalEquipmentRepository extends BaseRepository implements CategoryMedicalEquipmentContract
{
    /**
     * AcademicDegreeRepository constructor.
     * @param AcademicDegree $model
     */
    public function __construct(CategoryMedicalEquipment $model)
    {
        parent::__construct($model);
    }
}
