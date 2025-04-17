<?php

namespace App\Repositories\SQL;

use App\Models\StaticPage;
use App\Repositories\Contracts\StaticPageContract;
use Illuminate\Database\Eloquent\Collection;

class StaticPageRepository extends BaseRepository implements StaticPageContract
{
    /**
     * StaticPageRepository constructor.
     * @param StaticPage $model
     */
    public function __construct(StaticPage $model)
    {
        parent::__construct($model);
    }

    public function getByPage(string $page): ?StaticPage
    {
        try {
            return $this->model->where('page', $page)->first();
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve static page: ' . $e->getMessage());
            return null;
        }
    }
    
}
