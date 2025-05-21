<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use App\Repositories\Contracts\BannerContract;
use Illuminate\Http\Request;

class BannerController extends BaseApiController
{
    public function __construct(BannerContract $contract)
    {
        parent::__construct($contract, BannerResource::class);
    }


}
