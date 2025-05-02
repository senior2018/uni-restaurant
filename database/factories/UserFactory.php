<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'phone' => $this->faker->phoneNumber(),
            'permanent_location' => $this->faker->address(),
            'role' => 'customer',
            'failed_login_attempts' => 0,
            'remember_token' => Str::random(10),
        ];
    }

    //State for admin user
    public function admin(): static
    {
        return $this->state([

            'role' => 'admin',
            'email' => 'admin@university.com' //fixed email for admin
        ]);
    }

    //State for staff users
    public function staff(): static
    {
        return $this->state([
            'role' => 'staff',
            'email' => 'staff@university.com',
        ]);
    }

    //state for unverified users
    public function unverified(): static
    {
        return $this->state([
            'email_verified_at' => null,
        ]);
    }
}
