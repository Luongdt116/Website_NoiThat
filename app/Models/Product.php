<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'price', 'discount_percent', 'stock', 'material', 'image', 'category_id'];

    protected $casts = ['price' => 'decimal:2', 'discount_percent' => 'integer', 'stock' => 'integer'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Đang có giảm giá? (0–90%)
    public function hasDiscount(): bool
    {
        return $this->discount_percent > 0;
    }

    // Giá sau giảm (làm tròn về nghìn đồng) — dùng hiển thị và tính tiền đơn hàng
    public function getFinalPriceAttribute(): float
    {
        if (! $this->hasDiscount()) {
            return (float) $this->price;
        }

        return round((float) $this->price * (100 - $this->discount_percent) / 100, -3);
    }
}
