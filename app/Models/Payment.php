<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    const STRIPE_PAYMENT_METHOD = 'stripe';
    const PAYPAL_PAYMENT_METHOD = 'paypal';

    const PENDING_PAYMENT_METHOD = 'pending';
    const COMPLETED_PAYMENT_METHOD = 'completed';
    const FAILED_PAYMENT_METHOD = 'failed';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
