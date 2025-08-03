<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'bank_name',
        'account_number',
        'agency_number',
        'balance',
        'type',
        'description',
        'is_active',
        'user_id'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
        'date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function incomingTransfers()
    {
        return $this->hasMany(Transaction::class, 'to_account_id');
    }

    public function updateBalance()
    {
        $this->balance = $this->transactions()
            ->where('type', 'income')
            ->sum('amount') -
            $this->transactions()
            ->where('type', 'expense')
            ->sum('amount');

        $this->save();
    }
}
