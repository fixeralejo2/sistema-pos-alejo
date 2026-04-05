<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layaway extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id', 'user_id', 'sale_id', 'status',
        'minimum_percent', 'minimum_amount', 'due_date', 'notes'
    ];

    protected $casts = [
        'minimum_percent' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function layawayPayments(): HasMany
    {
        return $this->hasMany(LayawayPayment::class);
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->layawayPayments()->sum('amount');
    }

    public function getRemainingAttribute(): float
    {
        return $this->sale ? ($this->sale->total - $this->total_paid) : 0;
    }
}
