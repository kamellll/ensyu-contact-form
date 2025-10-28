<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $buildingNames = [
            'サンシャインビル',
            'グリーンハイツ',
            'メゾン桜丘',
            'コーポ白樺',
            'スカイタワー',
            'リバーサイドマンション',
            'パークサイドヒルズ',
        ];
        return [
            'category_id' => $this->faker->numberBetween(1, 3),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'gender' => $this->faker->numberBetween(1, 3),
            'email' => $this->faker->email(),
            'tel' => preg_replace('/\D/', '', $this->faker->phoneNumber()),
            'address' => $this->faker->prefecture() . $this->faker->city() . $this->faker->streetAddress(),
            'building' => $this->faker->randomElement($buildingNames) . $this->faker->numerify('###号室'),
            'detail' => $this->faker->realText(80),
        ];
    }
}
