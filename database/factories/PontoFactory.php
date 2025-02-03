<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

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

        $diffInSeconds = $saida->getTimestamp() - $entrada->getTimestamp();
        $totalHours = $diffInSeconds / 3600;
        $horasExtras = $totalHours > $expediente ? round($totalHours - $expediente, 2) : 0;

        return [
            'user_id' => $user->id,
            'entrada' => $entrada->format('H:i:s'),
            'saida' => $saida->format('H:i:s'),
            'horas_extras' => $horasExtras,
        ];
    }
}
