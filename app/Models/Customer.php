<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'email', 'phone', 'shipping_address', 'shipping_city',
        'shipping_state', 'shipping_zip',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
