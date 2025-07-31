<?php

namespace App\DTOs\Attendance;

class CheckInDTO
{

    public static function fromRequest($request)
    {
        return new self();
    }
}
