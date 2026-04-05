<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use SoftDeletes;

    protected $fillable = ['product_id', 'color', 'material', 'size', 'stock', 'barcode', 'additional_price'];

    protected $casts = ['additional_price' => 'decimal:2'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function getFinalPriceAttribute(): float
    {
        return $this->product->price + $this->additional_price;
    }

    public function getNameAttribute(): string
    {
        $parts = array_filter([$this->color, $this->material, $this->size]);
        return implode(' / ', $parts) ?: 'Sin variante';
    }
}
