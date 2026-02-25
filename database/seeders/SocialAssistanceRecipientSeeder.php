<?php

namespace Database\Seeders;

use App\Models\HeadOfFamily;
use Illuminate\Database\Seeder;
use App\Models\SocialAssistance;
use App\Models\SocialAssistanceRecipient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory;

class SocialAssistanceRecipientSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $socialAssistanceList = SocialAssistance::all();
        $headOfFamilies = HeadOfFamily::skip(1)->take(5)->get();
        $faker = Factory::create('id_ID');

        foreach ($headOfFamilies as $headOfFamily) {
            // Pilih 1 atau 2 bantuan secara acak untuk setiap kepala keluarga
            $randomAssistance = $socialAssistanceList->random(); 

            SocialAssistanceRecipient::create([
                'social_assistance_id' => $randomAssistance->id,
                'head_of_family_id'    => $headOfFamily->id,
                'bank'                 => $faker->randomElement(['BRI', 'BNI', 'Mandiri']),
                'amount'               => 500000,
                'account_number'       => $faker->bankAccountNumber(),
                'proof'                => '',    
                'status'               => 'approved',
            ]);
        }
    }
}
