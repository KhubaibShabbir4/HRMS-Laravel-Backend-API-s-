<?php

namespace App\Services;

use App\Models\ErrorLog;
use Throwable;
class ErrorLoggingService
{
    public static function log(Throwable $e){

        ErrorLog::create([
            'message'=>$e->getMessage(),
            'file'=>$e->getFile(),
            'line'=>$e->getLine(),
            'trace' => $e->getTraceAsString(),
            'user_id'=>auth()->check()?auth()->id():null,
        ]);
    }


}
