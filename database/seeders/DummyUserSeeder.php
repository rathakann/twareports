<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DummyUserSeeder extends Seeder
{
    /**
     * Run the database seeds to generate 1,000 dummy users with the 'User' role.
     */
    public function run(): void
    {
        $userRole = Role::firstOrCreate([
            'name' => 'User',
            'guard_name' => 'web',
        ]);

        $now = now();
        $password = Hash::make('password');
        $total = 1000;
        $chunkSize = 100;
        $faker = fake();

        DB::transaction(function () use ($userRole, $now, $password, $total, $chunkSize, $faker): void {
            for ($i = 0; $i < $total; $i += $chunkSize) {
                $usersData = [];
                for ($j = 0; $j < $chunkSize; $j++) {
                    $index = $i + $j + 1;
                    $firstName = $faker->firstName();
                    $lastName = $faker->lastName();
                    $name = "{$firstName} {$lastName}";
                    $emailSlug = Str::slug("{$firstName}.{$lastName}", '.');
                    $email = "{$emailSlug}.{$index}@example.com";
                    $createdAt = $now->copy()->subMinutes(rand(1, 60000));

                    $usersData[] = [
                        'name' => $name,
                        'email' => $email,
                        'password' => $password,
                        'status' => $faker->randomElement(['active', 'active', 'active', 'inactive']),
                        'is_admin' => false,
                        'email_verified_at' => $now,
                        'remember_token' => Str::random(10),
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];
                }

                User::insert($usersData);
            }

            // Find all users without any assigned roles and link them to the User role
            $unassignedUserIds = User::whereDoesntHave('roles')->pluck('id');

            $roleRelations = $unassignedUserIds->map(function ($userId) use ($userRole): array {
                return [
                    'role_id' => $userRole->id,
                    'model_type' => User::class,
                    'model_id' => $userId,
                ];
            })->all();

            foreach (array_chunk($roleRelations, 200) as $chunk) {
                DB::table('model_has_roles')->insert($chunk);
            }

            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        });

        $this->command?->info("Successfully seeded {$total} dummy users with 'User' role.");
    }
}
