<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\jobs_post;

class JobsPostFactory extends Factory
{
    protected $model = jobs_post::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->jobTitle,
            'user_id' => rand(1,2),
            'job_type_id' => rand(1,5),
            'category_id' => rand(1,5),
            'vacancy' => rand(1,5),
            'location' => $this->faker->city,
            'description' => $this->faker->text,
            'experience' => rand(1,10),
            'company_name' => $this->faker->company,
        ];
    }
}
