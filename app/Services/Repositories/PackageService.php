<?php

namespace App\Services\Repositories;

use App\Repositories\Contracts\ConsultationContract;
use App\Repositories\Contracts\PackageContract;

class PackageService
{
    private ConsultationContract $consultationContract;
    private PackageContract $packageContract;

    public function __construct(ConsultationContract $consultationContract, PackageContract $packageContract)
    {
        $this->consultationContract = $consultationContract;
        $this->packageContract = $packageContract;
    }

    public function subscribe($package, $data)
    {
        //
    }
}
