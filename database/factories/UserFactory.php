<?php
namespace Database\Factories;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class UserFactory extends Factory
{
    protected static ?string $password;
    public function definition(): array
    {
        return [
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'role' => 'admin',
            'admin_id' => null,
            'est_actif' => true,
            'departement' => fake()->company(),
            'telephone' => fake()->phoneNumber(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }
    public function superAdmin(): static
    {
        return $this->state(fn () => [
            'role' => 'super_admin',
            'admin_id' => null,
            'departement' => 'Plateforme',
        ]);
    }
    public function admin(): static
    {
        return $this->state(fn () => [
            'role' => 'admin',
            'admin_id' => null,
            'departement' => fake()->company(),
        ]);
    }
    public function employe(int $adminId): static
    {
        return $this->state(fn () => [
            'role' => 'employe',
            'admin_id' => $adminId,
        ]);
    }
    public function unverified(): static
    {
        return $this->state(fn () => [
            'email_verified_at' => null,
        ]);
    }
}
