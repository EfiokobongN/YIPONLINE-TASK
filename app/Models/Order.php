<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    const STATUS_PENDING    = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED    = 'shipped';
    const STATUS_DELIVERED  = 'delivered';
    const STATUS_CANCELLED  = 'cancelled';

    protected $fillable = [
        'user_id', 'order_number', 'status', 'subtotal', 'tax',
        'shipping', 'total', 'notes', 'paid_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            $order->order_number = 'YIP-' . strtoupper(uniqid());
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'pending'    => 'badge-warning',
            'processing' => 'badge-info',
            'shipped'    => 'badge-primary',
            'delivered'  => 'badge-success',
            'cancelled'  => 'badge-danger',
            default      => 'badge-secondary',
        };
    }
    public static function statuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_SHIPPED,
            self::STATUS_DELIVERED,
            self::STATUS_CANCELLED,
        ];
    }
}
