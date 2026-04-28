<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name'        => $this->faker->name(),
            'email'       => $this->faker->unique()->safeEmail(),
            'phone'       => $this->faker->phoneNumber(),
            'role'        => 'customer',
            'is_active'   => true,
            'is_verified' => true,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => 'admin']);
    }

    public function agent(): static
    {
        return $this->state(fn () => ['role' => 'agent']);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function locked(): static
    {
        return $this->state(fn () => [
            'locked_until'          => now()->addHour(),
            'failed_login_attempts' => 5,
        ]);
    }
}
