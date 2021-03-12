<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    const TYPE_DEPOSIT = 'deposit';
    const TYPE_WITHDRAW = 'withdraw';
    const TYPE_TRANSFER = 'transfer';
    const TYPE_CHARGEBACK = 'chargeback';

    protected $fillable = [
        'payee',
        'payer',
        'amount',
        'type'
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
