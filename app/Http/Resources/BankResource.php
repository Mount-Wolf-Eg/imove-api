<?php

namespace App\Http\Resources;


use \Illuminate\Http\Request;

class BankResource extends BaseResource
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
            'id' => $this->id
        ];

        $this->mini = [
            'name' => $this->name,
            'account_number' => $this->account_number,
            'iban' => $this->iban,
        ];

        $this->full = [];

        $this->relations = [];

        return $this->getResource();
    }
}
