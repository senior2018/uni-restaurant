<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'order_id',
        'meal_id',
        'rating',
        'comment'
    ];

    /**
     * Get the user who submitted this rating.
     * @return BelongsTo<User, Rating>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order this rating is for.
     * @return BelongsTo<Order, Rating>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the meal being rated.
     * @return BelongsTo<Meal, Rating>
     */
    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }
}
