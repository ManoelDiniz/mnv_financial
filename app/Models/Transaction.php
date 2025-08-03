<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'amount',
        'type',
        'date',
        'description',
        'account_id',
        'category_id',
        'user_id',
        'to_account_id',
        'converted_amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'converted_amount' => 'decimal:2',
        'date' => 'date'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isTransfer()
    {
        return $this->type === 'transfer';
    }

    protected static function booted()
    {
        static::created(function ($transaction) {
            $transaction->account->updateBalance();
            if ($transaction->isTransfer() && $transaction->toAccount) {
                $transaction->toAccount->updateBalance();
            }
        });

        static::updated(function ($transaction) {
            $transaction->account->updateBalance();
            if ($transaction->isTransfer() && $transaction->toAccount) {
                $transaction->toAccount->updateBalance();
            }
        });

        static::deleted(function ($transaction) {
            $transaction->account->updateBalance();
            if ($transaction->isTransfer() && $transaction->toAccount) {
                $transaction->toAccount->updateBalance();
            }
        });
    }
}
