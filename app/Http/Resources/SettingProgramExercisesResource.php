<?php

namespace App\Http\Resources;

use \Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;


class SettingProgramExercisesResource extends BaseResource
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
            'consultation_id' => $this->consultation_id?? null,
            'diagnosis' => $this->diagnosis?? null,
            'num_of_sessions_per_day' => $this->num_of_sessions_per_day?? null,
            'num_of_days_of_week' => $this->num_of_days_of_week?? null,
            'num_of_weeks' => $this->num_of_weeks?? null,
            'break_between_exercises' => $this->break_between_exercises?? null,
            
        ];
        $this->full = [];
        $this->relations = [

        ];

        return $this->getResource();
    }
}