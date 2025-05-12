<?php

namespace App\Http\Resources;

use \Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;


class ProgramExercisesResource extends BaseResource
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
            // 'id' => $this->id,
        ];
        $this->mini = [
            // 'consultation_id' => $this->consultation_id,
            // 'diagnosis' => $this->diagnosis,
            // 'num_of_sessions_per_day' => $this->num_of_sessions_per_day,
            // 'num_of_days_of_week' => $this->num_of_days_of_week,
            // 'num_of_weeks' => $this->num_of_weeks,
            // 'break_between_exercises' => $this->break_between_exercises,
            'exercises' => $this->whenLoaded('program', function () use ($locale) {
                return $this->program->exercises->map(function ($exercise) use ($locale) {
                    return [
                        'id' => $exercise->id,
                        // 'name' => $exercise->getTranslation('name', $locale),
                        'name' => $exercise->name,
                        'brief' => $exercise->brief,
                        'description' => $exercise->description,
                        'sets' => $exercise->pivot->sets,
                        'break_between_sets' => $exercise->pivot->break_between_sets,
                        'weight' => $exercise->pivot->weight,
                        'rep' => $exercise->pivot->rep,
                        'hold_duration' => $exercise->pivot->hold_duration,
                        'comments' => $exercise->pivot->comments,
                        'media' => $exercise->relationLoaded('media') && $exercise->media ? new FileResource($exercise->media) : null,
                    ];
                });
            }),
            
        ];
        $this->full = [];
        $this->relations = [

        ];

        return $this->getResource();
    }
}