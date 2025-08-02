<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_id',
        'type',
        'amount',
        'description',
        'notes',
        'category',
        'transaction_date',
        'is_recurring',
        'recurring_frequency',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'is_recurring' => 'boolean',
    ];

    /**
     * Get the account that owns the transaction.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Scope for income transactions.
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope for expense transactions.
     */
    public function scopeExpenses($query)
    {
        return $query->where('type', 'expense');
    }

    /**
     * Scope for transfer transactions.
     */
    public function scopeTransfers($query)
    {
        return $query->where('type', 'transfer');
    }

    /**
     * Get formatted amount with currency symbol.
     */
    public function getFormattedAmountAttribute(): string
    {
        $prefix = $this->type === 'income' ? '+' : '-';
        $symbol = 'R$'; // Could be dynamic based on account currency
        
        return $prefix . $symbol . ' ' . number_format($this->amount, 2, ',', '.');
    }

    /**
     * Get transaction type in Portuguese.
     */
    public function getTypeNameAttribute(): string
    {
        $types = [
            'income' => 'Receita',
            'expense' => 'Despesa',
            'transfer' => 'Transferência',
        ];

        return $types[$this->type] ?? $this->type;
    }
}
