<?php

namespace App\Repositories\SQL;

use App\Models\SettingPackage;
use App\Repositories\Contracts\SettingPackageContract;

class SettingPackageRepository extends BaseRepository implements SettingPackageContract
{
    /**
     * SettingPackageRepository constructor.
     * @param SettingPackage $model
     */
    public function __construct(SettingPackage $model)
    {
        parent::__construct($model);
    }
}
