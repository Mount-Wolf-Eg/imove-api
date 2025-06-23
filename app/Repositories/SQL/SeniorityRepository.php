<?php

namespace App\Repositories\SQL;

use App\Models\Seniority;
use App\Repositories\Contracts\SeniorityContract;

class SeniorityRepository extends BaseRepository implements SeniorityContract
{
    /**
     * SeniorityRepository constructor.
     * @param Seniority $model
     */
    public function __construct(Seniority $model)
    {
        parent::__construct($model);
    }
}
