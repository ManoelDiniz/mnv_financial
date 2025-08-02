<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'balance',
        'currency',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transactions for this account.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Scope to get only active accounts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get formatted balance with currency symbol.
     */
    public function getFormattedBalanceAttribute(): string
    {
        $symbols = [
            'BRL' => 'R$',
            'USD' => '$',
            'EUR' => '€',
        ];

        $symbol = $symbols[$this->currency] ?? $this->currency;
        
        return $symbol . ' ' . number_format($this->balance, 2, ',', '.');
    }

    /**
     * Get account type in Portuguese.
     */
    public function getTypeNameAttribute(): string
    {
        $types = [
            'checking' => 'Conta Corrente',
            'savings' => 'Poupança',
            'credit_card' => 'Cartão de Crédito',
            'investment' => 'Investimento',
            'other' => 'Outros',
        ];

        return $types[$this->type] ?? $this->type;
    }
}
