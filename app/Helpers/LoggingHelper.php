<?php
// app/Helpers/LoggingHelper.php

use App\Models\ErrorLog;

if (!function_exists('log_error')) {
    function log_error(\Throwable $e, string $level = 'error'): void
    {
        ErrorLog::create([
            'level' => $level,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace'=> $e->getTraceAsString(),
        ]);
    }
}
