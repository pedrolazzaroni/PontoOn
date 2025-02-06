<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ponto>
 */
class PontoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get a random user
        $user = User::inRandomOrder()->first();
        $expediente = $user->expediente ?? 8;

        // Generate a date within the last month (only weekdays)
        $startDate = Carbon::now()->subMonth();
        $endDate = Carbon::now();

        do {
            $date = Carbon::createFromTimestamp(
                fake()->dateTimeBetween($startDate, $endDate)->getTimestamp()
            );
        } while ($date->isWeekend()); // Skip weekends

        // Generate entry time between 7:00 and 9:00
        $entrada = Carbon::create(
            $date->year,
            $date->month,
            $date->day,
            fake()->numberBetween(7, 9),
            fake()->numberBetween(0, 59),
            fake()->numberBetween(0, 59)
        );

        // Occasionally generate longer workdays for overtime
        $shouldHaveOvertime = fake()->boolean(30); // 30% chance of overtime
        $exitHour = $shouldHaveOvertime ?
            fake()->numberBetween(18, 20) : // Overtime: exit between 18:00 and 20:00
            fake()->numberBetween(16, 18);  // Normal: exit between 16:00 and 18:00

        $saida = Carbon::create(
            $date->year,
            $date->month,
            $date->day,
            $exitHour,
            fake()->numberBetween(0, 59),
            fake()->numberBetween(0, 59)
        );

        // Calculate total worked time
        $workedSeconds = $entrada->diffInSeconds($saida);
        $expedienteEmSegundos = $expediente * 3600; // Converting hours to seconds

        // Calculate overtime
        $horasExtras = '00:00:00';
        if ($workedSeconds > $expedienteEmSegundos) {
            $overtimeSeconds = $workedSeconds - $expedienteEmSegundos;
            $horasExtras = gmdate('H:i:s', $overtimeSeconds);
        }

        // Calculate late time
        $atraso = '00:00:00';
        $limiteEntrada = $date->copy()->setTime(9, 0, 0);
        if ($entrada->greaterThan($limiteEntrada)) {
            $segundosAtraso = $entrada->diffInSeconds($limiteEntrada);
            $atraso = gmdate('H:i:s', $segundosAtraso);
        }

        // Calculate worked hours
        $horasTrabalhadas = gmdate('H:i:s', $workedSeconds);

        return [
            'user_id' => $user->id,
            'entrada' => $entrada,
            'saida' => $saida,
            'horas_trabalhadas' => $horasTrabalhadas,
            'horas_extras' => $horasExtras,
            'atraso' => $atraso,
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }
}
