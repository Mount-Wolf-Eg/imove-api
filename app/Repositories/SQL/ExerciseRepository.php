<?php

namespace App\Repositories\SQL;

use App\Models\Exercise;
use App\Repositories\Contracts\ExerciseContract;
use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator;


class ExerciseRepository extends BaseRepository implements ExerciseContract
{
    /**
     * ExerciseRepository constructor.
     * @param Bank $model
     */
    public function __construct(Exercise $model)
    {
        parent::__construct($model);
    }


    /**
     * Search exercises with applied filters and relations.
     *
     * @param array $filters
     * @param array $relations
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function searchApi(array $filters = [], array $relations = [], array $data = []): LengthAwarePaginator
    {
        $query = $this->model::query();

        // Apply relations
        if (!empty($relations)) {
            $query->with($relations);
        }

        // Apply filters
        $this->applyFilters($query, $filters);

        // Apply ordering
        if (!empty($data['order'])) {
            foreach ((array) $data['order'] as $column => $direction) {
                $query->orderBy($column, $direction);
            }
        }

        // Apply pagination
        $limit = $data['limit'] ?? 10;
        $page = $data['page'] ?? 1;

        return $query->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * Apply filters to the query.
     *
     * @param Builder $query
     * @param array $filters
     * @return Builder
     */
    protected function applyFilters(Builder $query, array $filters): Builder
    {
        // Filter by medical_speciality_ids
        if (!empty($filters['medical_speciality_ids'])) {
            $query->whereHas('medicalSpecialities', function ($q) use ($filters) {
                $q->whereIn('medical_specialities.id', $filters['medical_speciality_ids']);
            });
        }

        // Filter by keyword
        if (!empty($filters['keyword'])) {
            $query->where(function ($q) use ($filters) {
                $q->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(name, "$.en")) LIKE ?', ['%' . $filters['keyword'] . '%'])
                  ->orWhereRaw('JSON_UNQUOTE(JSON_EXTRACT(name, "$.ar")) LIKE ?', ['%' . $filters['keyword'] . '%'])
                  ->orWhereRaw('JSON_UNQUOTE(JSON_EXTRACT(brief, "$.en")) LIKE ?', ['%' . $filters['keyword'] . '%'])
                  ->orWhereRaw('JSON_UNQUOTE(JSON_EXTRACT(brief, "$.ar")) LIKE ?', ['%' . $filters['keyword'] . '%'])
                  ->orWhereRaw('JSON_UNQUOTE(JSON_EXTRACT(description, "$.en")) LIKE ?', ['%' . $filters['keyword'] . '%'])
                  ->orWhereRaw('JSON_UNQUOTE(JSON_EXTRACT(description, "$.ar")) LIKE ?', ['%' . $filters['keyword'] . '%']);
            });
        }

        return $query;
    }

}