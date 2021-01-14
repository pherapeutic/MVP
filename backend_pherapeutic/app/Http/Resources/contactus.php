<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class contactus extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $request;
      /*  return [
            //'id' => $this->id,
            'message' => $request['message'],
            'screenshot1' => $this->screenshot1,
            'screenshot2' => $this->screenshot2,
            'screenshot3' => $this->screenshot3,
        //  'created_at' => $this->created_at->format('d/m/Y'),
          //  'updated_at' => $this->updated_at->format('d/m/Y'),
        ];*/
    }
}
