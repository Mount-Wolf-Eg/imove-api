<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Resources\ExerciseResource;
use App\Models\Exercise;
use App\Repositories\Contracts\ExerciseContract;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExerciseController extends BaseApiController
{
    protected array $relations = ['media', 'medicalSpecialities'];

    /**
     * ExerciseController constructor.
     * @param ExerciseContract $contract
     */
    public function __construct(ExerciseContract $contract)
    {
        parent::__construct($contract, ExerciseResource::class);
    }


    /**
     * Display a listing of exercises with optional filters.
     *
     * @param Request $request
     * @return mixed
     */
    public function allExercises(Request $request): mixed
    {
        try {
            $filters = array_merge($request->all(), $this->defaultScopes);

            // Apply medical_speciality_ids filter
            if ($request->has('medical_speciality_ids')) {
                $medicalSpecialityIds = is_array($request->input('medical_speciality_ids'))
                    ? $request->input('medical_speciality_ids')
                    : explode(',', $request->input('medical_speciality_ids'));

                $filters['medical_speciality_ids'] = array_filter($medicalSpecialityIds, 'is_numeric');
            }

            // Apply keyword filter (already supported by SearchTrait)
            if ($request->has('keyword')) {
                $filters['keyword'] = $request->input('keyword');
            }

            // Fetch exercises with filters and relations
            $data = array_merge($request->all(), [
                'order' => $request->input('order', []),
                'limit' => $request->input('limit', 10),
                'page' => $request->input('page', 1),
            ]);

            $exercises = $this->contract->searchApi($filters, $this->relations, $data);

            return $this->respondWithCollection($exercises);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }

}