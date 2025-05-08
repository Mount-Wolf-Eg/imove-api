<?php

namespace App\Http\Resources;

use \Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;


class SessionAnalyticsResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {

        $this->micro = [
        ];
        $this->mini = [
            'exercise_commitment' => [
                'total_exercises' => $this->resource['total_exercises'],
                'completed_exercises' => $this->resource['completed_exercises'],
            ],
            'session_details' => [
                'degree_of_pain' => $this->resource['session_details']['degree_of_pain'],
                'extent_of_improvement' => $this->resource['session_details']['extent_of_improvement'],
                'comments' => $this->resource['session_details']['comments'],
            ],
            'exercises_by_difficulty' => $this->resource['exercises_by_difficulty']->map(function ($exercise) {
                return [
                    'exercise_id' => $exercise->exercise_id,
                    'exercise_name' => $exercise->exercise->name,
                    'ease_of_exercise' => $exercise->ease_of_exercise,
                ];
            }),
            'skipped_exercises' => $this->resource['skipped_exercises']->map(function ($exercise) {
                return [
                    'exercise_id' => $exercise->exercise_id,
                    'exercise_name' => $exercise->exercise->name,
                    'reason_for_overtaking' => $exercise->reason_for_overtaking,
                ];
            }),
            'half_or_less_completed_sets' => $this->resource['half_or_less_completed_sets']->map(function ($exercise) {
                return [
                    'exercise_id' => $exercise->exercise_id,
                    'exercise_name' => $exercise->exercise->name,
                    'required_sets' => $exercise->sets,
                    'patient_total_sets' => $exercise->patient_total_sets,
                ];
            }),
        
        ];
        $this->full = [];
        $this->relations = [

        ];

        return $this->getResource();
    }
}