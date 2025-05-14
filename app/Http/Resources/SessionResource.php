<?php

namespace App\Http\Resources;

use \Illuminate\Http\Request;


class SessionResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $locale = $request->header('Accept-Language', config('app.locale'));

        $this->micro = [
            'id' => $this->id,
        ];
        $this->mini = [
            'consultation_id' => $this->consultation_id,
            'program_id' => $this->program_id,
            'week'       => $this->week,
            'day'        => $this->day,
            'degree_of_pain' => $this->degree_of_pain,
            'extent_of_improvement' => $this->extent_of_improvement,
            'comments' => $this->comments,
            'end_date' => $this->end_date,
            // 'sessionExercises' => $this->sessionExercises,
            'sessionExercises' => $this->whenLoaded('sessionExercises', fn () => $this->sessionExercises->map(fn ($exercise) => [
                'id' => $exercise->pivot->id,
                'program_id'  => $exercise->pivot->program_id,
                'session_id'  => $exercise->pivot->session_id,
                'exercise_id' => $exercise->id,
                'exercise_name' => $exercise->name,
                // 'exercise_name' => $exercise->getTranslation('name', $locale),
                'exercise_brief'       => $exercise->brief,
                'exercise_description' => $exercise->description,
                'sets' => $exercise->pivot->sets,
                'break_between_sets' => $exercise->pivot->break_between_sets,
                'weight' => $exercise->pivot->weight,
                'rep' => $exercise->pivot->rep,
                'hold_duration' => $exercise->pivot->hold_duration,
                'comments' => $exercise->pivot->comments,
                'ease_of_exercise' => $exercise->pivot->ease_of_exercise,
                'reason_for_overtaking' => $exercise->pivot->reason_for_overtaking,
                'complete_sets' => $exercise->pivot->complete_sets,
                'patient_total_sets' => $exercise->pivot->patient_total_sets,
                'patient_total_reps' => $exercise->pivot->patient_total_reps,
                'patient_exercise_repetitions' => $exercise->pivot->patient_exercise_repetitions,
                'created_at' => $exercise->pivot->created_at?->format('Y-m-d H:i:s'),
                'exercise_media' => $exercise->relationLoaded('media') && $exercise->media ? new FileResource($exercise->media) : null,
                'exercise_main_image' => $exercise->relationLoaded('mainImage') && $exercise->mainImage ? new FileResource($exercise->mainImage) : null,
                // 'updated_at' => $exercise->pivot->updated_at?->format('Y-m-d H:i:s'),
            ])),

            'end_date' => $this->end_date?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
        $this->full = [];
        $this->relations = [
        
        ];

        return $this->getResource();
    }
}