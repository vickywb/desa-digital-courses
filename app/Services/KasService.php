<?php

namespace App\Services;

use App\Helpers\LoggerHelper;
use App\Models\Kas;
use App\Models\VillageProfile;
use App\Repository\KasRepository;
use Illuminate\Support\Facades\DB;

class KasService
{
    public function __construct(
        private KasRepository $kasRepository,
    ) {}

    public function getKasByVillageProfile(VillageProfile $villageProfile): ?Kas
    {
        return $this->kasRepository->findByVillageProfileId($villageProfile->id);
    }

    public function createOrUpdateKas(VillageProfile $villageProfile, array $data): Kas
    {
        return DB::transaction(function () use ($villageProfile, $data) {
            $kas = $this->kasRepository->findByVillageProfileId($villageProfile->id);

            if ($kas) {
                $kas->fill($data);
                $kas = $this->kasRepository->save($kas);

                LoggerHelper::info('Kas updated successfully', [
                    'village_profile_id' => $villageProfile->id,
                    'total_balance' => $data['total_balance'] ?? null,
                ]);
            } else {
                $kas = $this->kasRepository->save(new Kas([
                    'village_profile_id' => $villageProfile->id,
                    ...$data,
                ]));

                LoggerHelper::info('Kas created successfully', [
                    'village_profile_id' => $villageProfile->id,
                    'kas_id' => $kas->id,
                ]);
            }

            return $kas->fresh();
        });
    }
}
