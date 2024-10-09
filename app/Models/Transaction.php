<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'pay_user_id',
        'recipient_user_id',
        'amount',
        'transaction_number',
        'transaction_date',
        'service_charge',
        'remarks',
        'total_amount',
    ];

    public function payUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pay_user_id');
    }

    public function recipientUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }
}
