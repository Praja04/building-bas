<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cardnodevice' => $this->faker->randomNumber(9),
            'nik' => $this->faker->randomNumber(9),
            'nama_pegawai' => $this->faker->name(),
            'departemen' => $this->faker->word(),
            'jam_masuk' => $this->faker->dateTime(),
            'jam_keluar' => $this->faker->dateTime(),
            'is_doorprize' => $this->faker->boolean(),
        ];
    }
}
