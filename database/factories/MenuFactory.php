<?php

namespace Database\Factories;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Menu>
 */
class MenuFactory extends Factory
{
    protected $model = Menu::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nama' => $this->faker->words(2, true), // Nama menu
            'deskripsi' => $this->faker->sentence(), // Deskripsi menu
            'price' => $this->faker->randomFloat(2, 10, 1000), // Harga antara 10 - 1000
            'stok' => $this->faker->numberBetween(1, 500), // Stok antara 1 - 500
            'status' => true,
            'image' => 'menus/' . $this->faker->image(storage_path('app/public/menus'), 640, 480, 'food', false),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
