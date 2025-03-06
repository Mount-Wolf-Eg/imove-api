<?php

namespace App\Repositories\SQL;

use App\Models\ConsultationQuestion;
use App\Repositories\Contracts\ConsultationQuestionContract;

class ConsultationQuestionRepository extends BaseRepository implements ConsultationQuestionContract
{
    /**
     * ConsultationQuestionRepository constructor.
     * @param ConsultationQuestion $model
     */
    public function __construct(ConsultationQuestion $model)
    {
        parent::__construct($model);
    }
}
