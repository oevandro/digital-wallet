<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        $document = [rand(11111111111, 99999999999),rand(11111111111111, 99999999999999)];
        $types = ['common','shopp'];
        $type = $types[rand(0, 1)];

        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'document' => strval(($type == 'common') ? $document[0] : $document[1]),
            'type' => strval($type),
            'password' => '12345678'
        ];
    }
}
