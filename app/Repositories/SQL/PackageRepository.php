<?php

namespace App\Repositories\SQL;

use App\Models\Package;
use App\Repositories\Contracts\PackageContract;

class PackageRepository extends BaseRepository implements PackageContract
{
    /**
     * DoctorPackageRepository constructor.
     * @param Package $model
     */
    public function __construct(Package $model)
    {
        parent::__construct($model);
    }
}
