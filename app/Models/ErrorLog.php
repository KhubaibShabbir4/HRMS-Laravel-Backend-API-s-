<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'message', 'file', 'line', 'trace', 'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
