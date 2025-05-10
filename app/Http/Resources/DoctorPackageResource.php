<?php

namespace App\Http\Resources;


use \Illuminate\Http\Request;

class DoctorPackageResource extends BaseResource
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
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'num_of_sessions' => $this->num_of_sessions,
            'duration' => $this->duration,
            'price' => $this->price,
        ];
        $this->mini = [
            'is_active' => $this->is_active,
            'active_status' => $this->active_status,
            'active_class' => $this->active_class,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
        $this->full = [];
        //$this->relationLoaded()
        $this->relations = [
            'user' => $this->whenLoaded('user', function () {
                return new UserResource($this->user);
            }),
            'image' => $this->whenLoaded('image', function () {
                return new FileResource($this->image);
            }),
            'consultations' => $this->whenLoaded('consultations', function () {
                return ConsultationResource::collection($this->consultations);
            }),
            'subscriptions' => $this->whenLoaded('subscriptions', function () {
                return SubscriptionResource::collection($this->subscriptions);
            }),
        ];
        return $this->getResource();
    }
}
