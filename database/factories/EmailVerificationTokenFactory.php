<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EmailVerificationTokenFactory extends Factory
{
    protected $model = \App\Models\EmailVerificationToken::class;

    public function definition()
    {
        return [
            'email' => $this->faker->unique()->safeEmail,
            'token' => Str::random(64),
            'created_by' => null,
            'expires_at' => now()->addHours(48),
            'verified_at' => null,
        ];
    }
}
