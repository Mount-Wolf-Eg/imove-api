<?php

namespace App\Http\Resources;


use \Illuminate\Http\Request;

class ProgramResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request) : array
    {
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
            'exercises' => $this->exercises->map(function ($exercise) {
                $pivot = $exercise->pivot;
                return [
                    'id' => $exercise->id,
                    'name' => $exercise->name,
                    'sets' => $pivot->sets,
                    'break_between_sets' => $pivot->break_between_sets,
                    'weight' => $pivot->weight,
                    'rep' => $pivot->rep,
                    'hold_duration' => $pivot->hold_duration,
                    'comments' => $pivot->comments,
                ];
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
        $this->full = [
        ];
        $this->relations = [
            //
        ];
        return $this->getResource();
    }
}
