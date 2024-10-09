<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gender',
        'profile',
        'address',
        'nrc_front',
        'nrc_back',
        'nrc_number',
        'birth_date',
        'age',
        'work_id',
        'description'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function work(): BelongsTo
    {
        return $this->belongsTo(Work::class, 'work_id');
    }
}
