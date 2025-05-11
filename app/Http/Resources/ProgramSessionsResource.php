<?php

namespace App\Http\Resources;

use \Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;


class ProgramSessionsResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $groupedSessions = $this->collection->groupBy('week')->map(function ($weekSessions) use ($request) {
            return [
                'week' => $weekSessions->first()->week,
                'days' => $weekSessions->groupBy('day')->map(function ($daySessions) use ($request) {
                    return [
                        'day' => $daySessions->first()->day,
                        'created_at' => $daySessions->first()->created_at?->format('Y-m-d H:i:s'),
                        'sessions' => SessionResource::collection($daySessions)->toArray($request),
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();

        return $groupedSessions;
    }
    
}