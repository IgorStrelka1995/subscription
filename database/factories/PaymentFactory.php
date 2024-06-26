<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::all()->random()->id,
            'subscription_id' => Subscription::all()->random()->id,
            'amount' => $this->faker->randomFloat(2, 1, 800),
            'payment_method' => $this->faker->randomElement(['stripe', 'paypal']),
            'payment_status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
        ];
    }
}
