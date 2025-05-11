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
use Carbon\Carbon;


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


    /**
     * Update exercise in session in progres for a specific PatientSessionExercise.
     *
     * @param int $exerciseId
     * @param array $data
     * @return PatientSessionExercise
     * @throws \Exception
     */
    public function updateExerciseProgress(int $exerciseId, array $data): PatientSessionExercise
    {
        return DB::transaction(function () use ($exerciseId, $data) {
            $exercise = PatientSessionExercise::where('id', $exerciseId)->firstOrFail();
                // ->whereHas('program', function ($query) {
                //     $query->where('patient_id', auth()->user()->patient->id);
                // })
                // ->firstOrFail();

            // Calculate patient_total_sets and patient_total_reps
            $patientTotalSets = count($data['patient_exercise_repetitions']);
            $patientTotalReps = array_sum(array_column($data['patient_exercise_repetitions'], 'rep_number'));

            // Determine if complete_sets should be true
            $completeSets = $patientTotalSets >= $exercise->sets;

            // Update the exercise
            $exercise->update([
                'ease_of_exercise' => $data['ease_of_exercise'],
                'patient_exercise_repetitions' => $data['patient_exercise_repetitions'],
                'patient_total_sets' => $patientTotalSets,
                'patient_total_reps' => $patientTotalReps,
                'complete_sets' => $completeSets,
            ]);

            return $exercise->refresh();
        });
    }

    
    /**
     * Update/add exercise reason for overtaking for a specific PatientSessionExercise.
     *
     * @param int $exerciseId
     * @param string $reason
     * @return PatientSessionExercise
     * @throws \Exception
     */
    public function updateReasonForOvertaking(int $exerciseId, string $reason): PatientSessionExercise
    {
        return DB::transaction(function () use ($exerciseId, $reason) {
            $exercise = PatientSessionExercise::where('id', $exerciseId)->firstOrFail();

            $exercise->update([
                'reason_for_overtaking' => $reason,
            ]);

            return $exercise->refresh();
        });
    }

    
    /**
     * Update/end session details for a specific PatientSession.
     *
     * @param int $sessionId
     * @param array $data
     * @return PatientSession
     * @throws \Exception
     */
    public function updateSession(int $sessionId, array $data): PatientSession
    {
        return DB::transaction(function () use ($sessionId, $data) {
            $session = PatientSession::where('id', $sessionId)->firstOrFail();
                // ->whereHas('program', function ($query) {
                //     $query->where('patient_id', auth()->user()->patient->id);
                // })
                // ->firstOrFail();

            // Update the session
            $session->update([
                'degree_of_pain' => $data['degree_of_pain'],
                'extent_of_improvement' => $data['extent_of_improvement'],
                'comments' => $data['comments'],
                'end_date' => Carbon::now(),
            ]);

            return $session->refresh();
        });
    }

   
    /**
     * Get analytical report for a specific program.
     *
     * @param int $programId
     * @return array
     * @throws \Exception
     */
    public function patientAnalyzeProgram(Program $program): array
    {
        $programId = $program->id;
        // 1. Commitment Days
        $totalProgramDays = $program->num_of_days_of_week * $program->num_of_weeks;
        $completedDays = PatientSession::where('program_id', $programId)
            ->whereNotNull('end_date')
            ->distinct('week', 'day')
            ->count();

        // 2. Session Commitment
        $totalSessions = $program->num_of_sessions_per_day * $program->num_of_days_of_week * $program->num_of_weeks;
        $completedSessions = PatientSession::where('program_id', $programId)
            ->whereNotNull('end_date')
            ->count();

        // 3. Last 10 Sessions
        $lastSessions = PatientSession::where('program_id', $programId)
            ->whereNotNull('end_date')
            ->orderBy('end_date', 'desc')
            ->take(10)
            ->get(['degree_of_pain', 'extent_of_improvement']);

        $degreeOfPain = $lastSessions->pluck('degree_of_pain')->toArray();
        $extentOfImprovement = $lastSessions->pluck('extent_of_improvement')->toArray();

        // 4. Top 3 Difficult Exercises
        // We fetched the exercises with the average ease_of_exercise using AVG(ease_of_exercise).
        // We sorted them in descending order and took the highest
        $difficultExercises = PatientSessionExercise::where('program_id', $programId)
            ->groupBy('exercise_id')
            ->select('exercise_id', DB::raw('AVG(ease_of_exercise) as avg_ease'))
            ->orderBy('avg_ease', 'desc')
            ->take(3)
            ->with(['exercise' => fn ($query) => $query->select('id', 'name')])
            ->get()
            ->map(function ($item) {
                return [
                    'exercise_id' => $item->exercise_id,
                    'exercise_name' => $item->exercise->name,
                    'average_ease_of_exercise' => round($item->avg_ease, 2),
                ];
            })->toArray();
        
        // 5.0 Overperformed Exercises (Top 3 where patient_total_sets > sets)
           // ->select('exercise_id', DB::raw('COUNT(*) as excess_sets_count'))
        // 5.1 Overperformed Exercises (Top 3 based on average difference: patient_total_sets - sets)
        $overratedExercises = PatientSessionExercise::where('program_id', $programId)
        ->whereRaw('patient_total_sets > sets')
        ->groupBy('exercise_id')
        ->select('exercise_id', DB::raw('AVG(patient_total_sets - sets) as avg_sets_difference'))
        ->orderByDesc('avg_sets_difference')
        ->take(3)
        ->with(['exercise' => fn ($query) => $query->select('id', 'name')])
        ->get()
        ->map(function ($item) {
            return [
                'exercise_id' => $item->exercise_id,
                'exercise_name' => $item->exercise->name,
                'average_sets_difference' => round($item->avg_sets_difference, 2),
            ];
        })->toArray();

        // 6. Top 3 Incomplete Exercises
        $incompleteExercises = PatientSessionExercise::where('program_id', $programId)
            ->where('complete_sets', 0)
            ->whereNull('reason_for_overtaking')
            ->groupBy('exercise_id')
            ->select('exercise_id', DB::raw('AVG(patient_total_sets) as avg_sets'))
            ->orderBy('avg_sets', 'asc')
            ->take(3)
            ->with(['exercise' => fn ($query) => $query->select('id', 'name')])
            ->get()
            ->map(function ($item) {
                return [
                    'exercise_id' => $item->exercise_id,
                    'exercise_name' => $item->exercise->name,
                    'average_patient_total_sets' => round($item->avg_sets, 2),
                ];
            })->toArray();

        // 7. Top 3 Skipped Exercises
        $skippedExercises = PatientSessionExercise::where('program_id', $programId)
            ->whereNotNull('reason_for_overtaking')
            ->groupBy('exercise_id')
            ->select('exercise_id', DB::raw('COUNT(*) as count'))
            ->orderBy('count', 'desc')
            ->take(3)
            ->with(['exercise' => fn ($query) => $query->select('id', 'name')])
            ->get()
            ->map(function ($item) {
                return [
                    'exercise_id' => $item->exercise_id,
                    'exercise_name' => $item->exercise->name,
                    'skip_count' => $item->count,
                ];
            })->toArray();

        return [
            'commitment_days' => [
                'total_days' => $totalProgramDays,
                'completed_days' => $completedDays,
            ],
            'session_commitment' => [
                'total_sessions' => $totalSessions,
                'completed_sessions' => $completedSessions,
            ],
            'last_10_sessions' => [
                'degree_of_pain' => $degreeOfPain,
                'extent_of_improvement' => $extentOfImprovement,
            ],
            'top_difficult_exercises' => $difficultExercises,
            'top_overrated_exercises' => $overratedExercises,
            'top_incomplete_exercises' => $incompleteExercises,
            'top_skipped_exercises' => $skippedExercises,
        ];
    }
 
    /**
     * Get analytical report for a specific session.
     *
     * @param int $sessionId
     * @return array
     * @throws \Exception
     */
    public function patientSessionAnalytics(int $sessionId): array
    {
        $session = PatientSession::where('id', $sessionId)->firstOrFail();

        // 1. Exercise Commitment
        $totalExercises = PatientSessionExercise::where('session_id', $sessionId)->count();
        $completedExercises = PatientSessionExercise::where('session_id', $sessionId)
            ->where('complete_sets', true)
            ->count();

        // 2. Session Details
        $sessionDetails = [
            'degree_of_pain' => $session->degree_of_pain,
            'extent_of_improvement' => $session->extent_of_improvement,
            'comments' => $session->comments,
        ];

        // 3. Exercises by Difficulty (ordered by ease_of_exercise descending)
        $exercisesByDifficulty = PatientSessionExercise::where('session_id', $sessionId)
            ->whereNotNull('ease_of_exercise')
            ->orderBy('ease_of_exercise', 'desc')
            ->with(['exercise' => fn ($query) => $query->select('id', 'name')])
            ->get(['exercise_id', 'ease_of_exercise']);

        // 4. Skipped Exercises (with non-null reason_for_overtaking)
        $skippedExercises = PatientSessionExercise::where('session_id', $sessionId)
            ->whereNotNull('reason_for_overtaking')
            ->with(['exercise' => fn ($query) => $query->select('id', 'name')])
            ->get(['exercise_id', 'reason_for_overtaking']);

        // 5. Exercises with half or less completed sets (patient_total_sets <= sets / 2)
        $halfOrLessCompletedSets = PatientSessionExercise::where('session_id', $sessionId)
            ->whereRaw('patient_total_sets <= sets / 2')
            ->with(['exercise' => fn ($query) => $query->select('id', 'name')])
            ->get(['exercise_id', 'sets', 'patient_total_sets']);

        return [
            'total_exercises' => $totalExercises,
            'completed_exercises' => $completedExercises,
            'session_details' => $sessionDetails,
            'exercises_by_difficulty' => $exercisesByDifficulty,
            'skipped_exercises' => $skippedExercises,
            'half_or_less_completed_sets' => $halfOrLessCompletedSets,
        ];
    }

}