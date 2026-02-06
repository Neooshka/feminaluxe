<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataSubjectRequest extends Model
{
    protected $fillable = [
        'subject_name',
        'phone',
        'request_type',
        'content',
        'received_at',
        'deadline',
        'status',
        'response',
        'responded_at',
    ];
    
    protected $casts = [
        'received_at' => 'datetime',
        'deadline' => 'datetime',
        'responded_at' => 'datetime',
    ];
}