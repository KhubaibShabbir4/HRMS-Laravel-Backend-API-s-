<?php
namespace App\DTOs\Attendance;

class CheckOutDTO
{


    public static function fromRequest($request)
    {
        return new self();
    }
}
