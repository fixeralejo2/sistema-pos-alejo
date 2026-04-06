<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LayawayPayment extends Model
{
    protected $fillable = ['layaway_id', 'amount', 'payment_method', 'notes'];

    protected $casts = ['amount' => 'decimal:2'];

    public function layaway(): BelongsTo
    {
        return $this->belongsTo(Layaway::class);
    }
}
