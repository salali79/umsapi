<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BankResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return[
            'status' => 'success',
            'message' => 'bank resource',
            'data' => $this,
            'action' => ''
        ];
        //return parent::toArray($request);
    }
}
