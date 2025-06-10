<?php

namespace App\Repositories\SQL;

use App\Models\Banner;
use App\Repositories\Contracts\BannerContract;
use App\Repositories\Contracts\FileContract;
use App\Constants\FileConstants;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;


class BannerRepository extends BaseRepository implements BannerContract
{
    /**
     * BannerRepository constructor.
     * @param Bank $model
     */
    public function __construct(Banner $model)
    {
        parent::__construct($model);
    }


    // public function syncRelations($model, $attributes)
    // {
    //     if (isset($attributes['main_image'])) {
    //         if (is_file($attributes['main_image'])){
    //             $file = resolve(FileContract::class)->create(['file' => $attributes['main_image'],
    //                 'type' => FileConstants::FILE_TYPE_BANNER_IMAGE->value]);
    //         }else{
    //             $file = resolve(FileContract::class)->find($attributes['main_image']);
    //         }
    //         $model->mainImage()->save($file);
    //     }
    //     return $model;
    // }

    public static function syncRelations($model, $attributes)
    {
        if (isset($attributes['main_image'])) {
            if ($model->mainImage && $model->mainImage?->id != $attributes['main_image'])
                resolve(FileContract::class)->remove($model->mainImage);
            if (is_file($attributes['main_image'])) {
                $file = resolve(FileContract::class)->create([
                    'file' => $attributes['main_image'],
                    'type' => FileConstants::FILE_TYPE_BANNER_IMAGE->value
                ]);
            } else {
                $file = resolve(FileContract::class)->find($attributes['main_image']);
            }
            $model->mainImage()->save($file);
        }
        return $model;
    }


}
