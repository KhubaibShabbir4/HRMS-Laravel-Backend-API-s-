<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'user' => $this->user->name,
            'date' => $this->date,
            'check_in' => $this->check_in,
            'check_out' => $this->check_out,
        ];
    }
}
