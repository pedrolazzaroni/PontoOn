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
        $date = fake()->dateTimeBetween('-1 month', 'now');
        $entrada = fake()->dateTimeBetween($date->format('Y-m-d') . ' 07:00:00', $date->format('Y-m-d') . ' 09:00:00');
        $saida = fake()->dateTimeBetween($date->format('Y-m-d') . ' 16:00:00', $date->format('Y-m-d') . ' 18:00:00');

        $user = User::inRandomOrder()->first();
        $expediente = $user->expediente ?? 8;

        // Calculate hours difference
        $diffInSeconds = $saida->getTimestamp() - $entrada->getTimestamp();
        $expedienteEmSegundos = $expediente * 3600;

        // Calculate overtime in H:i:s format
        $horasExtras = '00:00:00';
        if ($diffInSeconds > $expedienteEmSegundos) {
            $segundosExtras = $diffInSeconds - $expedienteEmSegundos;
            $horasExtras = sprintf(
                "%02d:%02d:%02d",
                floor($segundosExtras / 3600),
                floor(($segundosExtras % 3600) / 60),
                $segundosExtras % 60
            );
        }

        return [
            'user_id' => $user->id,
            'entrada' => $entrada, // Full datetime
            'saida' => $saida,    // Full datetime
            'horas_extras' => $horasExtras,
        ];
    }
}
