<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $category_id = DB::table('categories')
            ->inRandomOrder()
            ->first();
        return [
            'category_id' => $category_id->id,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'gender' => fake()->randomElement([1, 2, 3]),
            'email' => fake()->email(),
            'tel' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'building' => fake()->sentence(),
            'detail' => fake()->sentence(),
            'created_at' => fake()->date(),
            'updated_at' => fake()->date(),
        ];
    }
}
