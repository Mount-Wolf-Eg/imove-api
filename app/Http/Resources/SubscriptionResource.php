<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class SubscriptionResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->micro = [
            'id' => $this->id,
            'package_id' => $this->package_id,
            'start_date' => $this->start_date?->format('Y-m-d H:i:s'),
            'end_date' => $this->end_date?->format('Y-m-d H:i:s'),
            'status' => $this->status,
            'is_active' => $this->is_active,
            'is_paid' => $this->is_paid,
            'amount' => $this->amount,
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
            'consultations' => $this->whenLoaded('consultations', function () {
                return ConsultationResource::collection($this->consultations);
            }),
            'package' => $this->whenLoaded('package', function () {
                return new DoctorPackageResource($this->package);
            }),
            'doctor' => $this->whenLoaded('doctor', function () {
                return new UserResource($this->doctor);
            }),
            'patient' => $this->whenLoaded('patient', function () {
                return new UserResource($this->patient);
            }),
            'coupon' => $this->whenLoaded('coupon', function () {
                return new CouponResource($this->coupon);
            }),
            'payment' => $this->whenLoaded('payment', function () {
                return new PaymentResource($this->payment);
            }),
        ];
        return $this->getResource();
    }
}
