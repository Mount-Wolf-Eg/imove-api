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

    public function subscribe($package)
    {
        $attributes                         = request()->validated();
        $attributes['doctor_id']            = $package->user_id;
        $attributes['package_id']           = $package->id;
        $attributes['is_active']            = true;
        $attributes['start_date']           = now();
        $attributes['end_date']             = now()->addDays($package->duration);
        $attributes['price']                = $package->price;
        $attributes['num_of_sessions']      = $package->num_of_sessions;
        $attributes['used_num_of_sessions'] = 1;

        return resolve(SubscriptionRepository::class)->create($attributes);
    }
}
