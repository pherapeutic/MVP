<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Faq extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'questions' => $this->questions,
            'answers' => $this->answers,
            'created_at' =>  date('Y-m-d', strtotime($this->created_at)),
            'updated_at' =>  date('Y-m-d', strtotime($this->updated_at))
        ];
    }
}
