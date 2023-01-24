<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

         User::factory()->create([
             'name' => 'Arafath',
             'email' => 'admin@huex.com',
             'role' => 'admin',
         ]);

         $subjects = ['sinhala', 'english', 'tamil', 'maths'];
         foreach ($subjects as $subject){
             Subject::factory()->create([
                'name' => $subject,
             ]);
         }

         $grades = ['7', '8', '9'];
        foreach ($grades as $grade){
            Grade::factory()->create([
                'name' => $grade,
            ]);
        }

    }
}
