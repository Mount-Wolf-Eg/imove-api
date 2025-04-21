<?php

namespace App\Repositories\SQL;

use App\Constants\FileConstants;
use App\Models\Package;
use App\Repositories\Contracts\FileContract;
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

    public function syncRelations($model, $attributes)
    {
        if (isset($attributes['image'])) {
            if ($model->image)
                resolve(FileContract::class)->remove($model->image);
            if (is_numeric($attributes['image'])) {
                $file = resolve(FileContract::class)->find($attributes['image']);
            } else {
                $file = resolve(FileContract::class)->create([
                    'file' => $attributes['image'],
                    'type' => FileConstants::FILE_TYPE_PACKAGE_IMAGE
                ]);
            }
            $model->image()->save($file);
        }
        return $model;
    }
}
