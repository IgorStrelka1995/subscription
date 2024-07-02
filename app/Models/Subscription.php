<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    const SUBSCRIPTION_STATUS_ACTIVE = 'active';
    const SUBSCRIPTION_STATUS_CANCELLED = 'cancelled';
    const SUBSCRIPTION_STATUS_EXPIRED = 'expired';

    protected $guarded = [];
}
