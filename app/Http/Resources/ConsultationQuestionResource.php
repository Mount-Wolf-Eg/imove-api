<?php

namespace App\Http\Resources;

use \Illuminate\Http\Request;

class ConsultationQuestionResource extends BaseResource
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
            'question' => $this->question,
            'answer' => $this->whenPivotLoaded('consultation_question', function () {
                return $this->pivot->answer;
            }),
        ];
        $this->mini = [
            'is_active' => $this->is_active,
            'active_status' => $this->active_status,
            'active_class' => $this->active_class,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
        $this->full = [
        ];
        //$this->relationLoaded()
        $this->relations = [
        ];
        return $this->getResource();
    }
}
