<?php

namespace App\Repositories\SQL;

use App\Models\SettingConsultation;
use App\Repositories\Contracts\SettingConsultationContract;

class SettingConsultationRepository extends BaseRepository implements SettingConsultationContract
{
    /**
     * SettingConsultationRepository constructor.
     * @param SettingPackage $model
     */
    public function __construct(SettingConsultation $model)
    {
        parent::__construct($model);
    }
}
