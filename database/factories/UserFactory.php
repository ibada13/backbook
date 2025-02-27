<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
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
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(), // Mark email as verified by default
            'password' => Hash::make('password'), // Use a consistent test password
            'remember_token' => Str::random(10),
            'user_pfp'=>$this->getRandomImage("users"),
            'Role'=>$this->assignRole(),
        ];
    }
    private function getRandomImage($folder)
    {
        $imageDirectory = public_path("images/{$folder}");
        $images = glob($imageDirectory . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        if (!$images || empty($images)) {
            return null;
        }
        return basename($images[array_rand($images)]);
    }
    private function assignRole()
    {
        $random = rand(1, 100);

        if ($random <= 94) {
            return User::ROLE_SLAVE;
        } elseif ($random <= 98) {
            return User::ROLE_CITIZEN;
        } else { 
            return User::ROLE_KING;
        }
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
