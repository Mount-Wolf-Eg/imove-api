<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ProgramResource extends BaseResource
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
            'diagnosis' => $this->diagnosis,
            'num_of_sessions_per_day' => $this->num_of_sessions_per_day,
            'num_of_days_of_week' => $this->num_of_days_of_week,
            'num_of_weeks' => $this->num_of_weeks,
            'break_between_exercises' => $this->break_between_exercises,
            'exercises' => $this->whenLoaded('exercises', function () use ($locale) {
                return $this->exercises->map(function ($exercise) use ($locale) {
                    return [
                        'id' => $exercise->id,
                        // 'name' => $exercise->getTranslation('name', $locale),
                        'name' => $exercise->name,
                        'sets' => $exercise->pivot->sets,
                        'break_between_sets' => $exercise->pivot->break_between_sets,
                        'weight' => $exercise->pivot->weight,
                        'rep' => $exercise->pivot->rep,
                        'hold_duration' => $exercise->pivot->hold_duration,
                        'comments' => $exercise->pivot->comments,
                    ];
                });
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
        $this->full = [];
        $this->relations = [
            // 'consultation' => $this->whenLoaded('consultation', fn () => [
            //     'id' => $this->consultation->id,
            //     'patient_id' => $this->consultation->patient_id,
            // ]),
            // 'sessions' => $this->whenLoaded('sessions', fn () => $this->sessions->map(fn ($session) => [
            //     'id' => $session->id,
            //     'session_date' => $session->session_date,
            // ])),
        ];

        return $this->getResource();
    }
}