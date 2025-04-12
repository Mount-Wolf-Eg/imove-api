<?php

namespace App\Repositories\SQL;

use App\Models\Bank;
use App\Repositories\Contracts\BankContract;

class BankRepository extends BaseRepository implements BankContract
{
    /**
     * BankRepository constructor.
     * @param Bank $model
     */
    public function __construct(Bank $model)
    {
        parent::__construct($model);
    }
}
