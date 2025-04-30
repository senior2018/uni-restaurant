<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meal extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'price',
        'category_id',
        'description',
        'image_url',
        'is_available'
    ];

    /**
     * Get the category this meal belongs to.
     * @return BelongsTo<MealCategory, Meal>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(MealCategory::class);
    }

    /**
     * Get all order items containing this meal.
     * @return HasMany<OrderItem>
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
