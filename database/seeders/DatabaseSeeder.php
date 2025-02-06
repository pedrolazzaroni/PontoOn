<?php

namespace Database\Seeders;

use App\Models\Ponto;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {


        // Create 10 regular users
        $users = [];
        for ($i = 1; $i <= 10; $i++) {
            $users[] = User::create([
                'name' => fake()->name(),
                'email' => "user{$i}@example.com",
                'password' => Hash::make('123456'),
                'status' => true,
                'responsavel_id' => 1,
                'expediente' => 8
            ]);
        }

        // Calculate working days in the last month
        $startDate = Carbon::now()->subMonth();
        $endDate = Carbon::now();
        $workingDays = 0;

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if (!$date->isWeekend()) {
                $workingDays++;
            }
        }

        // Create points for each user for each working day
        foreach ($users as $user) {
            Ponto::factory($workingDays)->create([
                'user_id' => $user->id
            ]);
        }

        // Log information about seeding
        \Log::info('Seeding completed', [
            'users_created' => count($users),
            'working_days' => $workingDays,
            'total_points' => $workingDays * count($users)
        ]);
    }
}
