<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\JobType;
use App\Models\jobs_post;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

         // Creating 5 dummy categories
         //Category::factory(5)->create();

         // Creating 5 dummy job types
         //JobType::factory(5)->create();
      

         //creating 50 dummy job posts
         jobs_post::factory(50)->create();
    }
}
