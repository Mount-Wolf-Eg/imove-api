<?php

namespace App\Repositories\SQL;

use App\Models\Program;
use App\Models\{PatientSession, PatientSessionExercise};
use App\Repositories\Contracts\ProgramContract;
use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

class ProgramRepository extends BaseRepository implements ProgramContract
{
    /**
     * ProgramRepository constructor.
     * @param Program $model
     */
    public function __construct(Program $model)
    {
        parent::__construct($model);
    }

    
    /**
     * Search programs with applied filters and relations.
     *
     * @param array $filters
     * @param array $relations
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function searchPatient(array $filters = [], array $relations = [], array $data = []): LengthAwarePaginator
    {
        $query = $this->model::query();
     
        if (!empty($filters['patient_id'])) {
            $query->where('patient_id', $filters['patient_id']);
        }

        // Apply pagination
        $limit = $data['limit'] ?? 10;
        $page = $data['page'] ?? 1;

        return $query->with($relations)->paginate($limit, ['*'], 'page', $page);
    }



    /**
     * Create a new session for the specified program.
     *
     * @param Program $program
     * @param array $data
     * @return PatientSession
     * @throws \Exception
     */
    public function createSession(Program $program, array $data): PatientSession
    {
        return DB::transaction(function () use ($program, $data) {
            // Check if there is an active session (end_date is NULL)
            $activeSession = PatientSession::where('program_id', $program->id)
                ->whereNull('end_date')
                ->exists();

            if ($activeSession) {
                throw new \Exception(__('messages.session_active'), 422);
            }

            // Validate week
            if ($data['week'] > $program->num_of_weeks) {
                throw new \Exception(__('messages.session_week_exceeded', ['max' => $program->num_of_weeks]), 422);
            }

            // Validate day
            if ($data['day'] > $program->num_of_days_of_week) {
                throw new \Exception(__('messages.session_day_exceeded', ['max' => $program->num_of_days_of_week]), 422);
            }

            // Check the number of sessions for the given day
            $sessionsToday = PatientSession::where('program_id', $program->id)
                ->where('week', $data['week'])
                ->where('day', $data['day'])
                ->count();

            if ($sessionsToday >= ($program->num_of_sessions_per_day + 5)) {
                throw new \Exception(__('messages.session_limit_exceeded', ['max' => ($program->num_of_sessions_per_day + 5)]), 422);
            }

            // Create the session
            $session = PatientSession::create(array_merge($data, [
                'program_id' => $program->id,
                'consultation_id' => $program->consultation_id,
            ]));

            // Create session exercises based on program exercises
            $programExercises = $program->exercises()->get();
            foreach ($programExercises as $programExercise) {
                $pivotData = $programExercise->pivot;

                PatientSessionExercise::create([
                    'program_id' => $program->id,
                    'session_id' => $session->id,
                    'exercise_id' => $programExercise->id,
                    'sets' => $pivotData->sets,
                    'break_between_sets' => $pivotData->break_between_sets,
                    'weight' => $pivotData->weight,
                    'rep' => $pivotData->rep,
                    'hold_duration' => $pivotData->hold_duration,
                    'comments' => $pivotData->comments,
                ]);
            }

            return $session;
        });
    }


}    
