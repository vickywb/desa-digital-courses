<?php

namespace Database\Seeders;

use App\Models\Development;
use Illuminate\Database\Seeder;
use App\Models\DevelopmentApplicant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DevelopmentApplicantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $developmentList = Development::all();
        $users = User::skip(1)->take(5)->get();

        foreach ($users as $user) {

            $randomDevelopment = $developmentList->random();
            DevelopmentApplicant::create([
                'development_id' => $randomDevelopment->id,
                'user_id' => $user->id,
                'status' => 'pending'
            ]);
        }

    }
}
