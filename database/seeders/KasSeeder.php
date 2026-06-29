<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Kas;
use App\Models\VillageProfile;
use Illuminate\Database\Seeder;

class KasSeeder extends Seeder
{
    public function run(): void
    {
        $villageProfile = VillageProfile::first();

        if (! $villageProfile) {
            $villageProfile = VillageProfile::create([
                'name' => 'Pondok Nirwana Anggaswangi',
                'about' => 'Desa digital yang berkomitmen pada pelayanan masyarakat.',
                'headman' => 'Budi Santoso',
                'people' => 1200,
                'agriculture_area' => 500,
                'total_area' => 1000,
            ]);
        }

        Kas::updateOrCreate(
            ['village_profile_id' => $villageProfile->id],
            [
                'total_balance' => 10_000_000,
                'monthly_expense' => 7_500_000,
                'iuran_kebersihan' => 65_000,
                'iuran_keamanan' => 50_000,
                'iuran_makam' => 5_000,
                'expense_keamanan' => 2_000_000,
                'expense_kebersihan' => 3_000_000,
            ]
        );
    }
}
