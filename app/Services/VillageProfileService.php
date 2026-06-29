<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\LoggerHelper;
use App\Models\VillageProfile;
use App\Repository\VillageProfileRepository;
use Illuminate\Support\Facades\DB;

class VillageProfileService
{
    public function __construct(
        private VillageProfileRepository $villageProfileRepository,
        private FileService $fileService,
    ) {}

    public function createVillageProfile(array $data, array $images = [])
    {
        try {
            return DB::transaction(function () use ($data, $images) {
                $villageProfile = $this->villageProfileRepository->save(new VillageProfile([
                    'name' => $data['name'],
                    'about' => $data['about'],
                    'headman' => $data['headman'],
                    'people' => $data['people'],
                    'agriculture_area' => $data['agriculture_area'],
                    'total_area' => $data['total_area'],
                ]));

                foreach ($images as $image) {
                    $fileId = $this->fileService->handleUploadAndSave($image, 'file/village-profiles')?->id;
                    $villageProfile->villageProfileFiles()->create(['file_id' => $fileId]);
                }

                LoggerHelper::info('Village profile created successfully', [
                    'village_profile_id' => $villageProfile?->id,
                    'name' => $villageProfile->name,
                ]);

                return $villageProfile->fresh('files');
            });
        } catch (\Throwable $th) {
            LoggerHelper::error('Failed to create village profile', [
                'error_message' => $th->getMessage(),
            ]);

            throw $th;
        }
    }

    public function updateVillageProfile(VillageProfile $villageProfile, array $data, array $images = [])
    {
        try {
            return DB::transaction(function () use ($villageProfile, $data, $images) {
                $villageProfile->fill($data);
                $villageProfile = $this->villageProfileRepository->save($villageProfile);

                if (! empty($images)) {
                    $oldFileIds = $villageProfile->files()->pluck('files.id')->toArray();

                    // Upload file baru dulu sebelum hapus yang lama
                    $newFileIds = [];
                    foreach ($images as $image) {
                        $fileId = $this->fileService->handleUploadAndSave($image, 'file/village-profiles')?->id;
                        if ($fileId) {
                            $newFileIds[] = $fileId;
                            $villageProfile->villageProfileFiles()->create(['file_id' => $fileId]);
                        }
                    }

                    // Hapus pivot & file lama setelah upload sukses
                    $villageProfile->villageProfileFiles()->whereIn('file_id', $oldFileIds)->delete();
                    foreach ($oldFileIds as $oldFileId) {
                        $this->fileService->deleteFile($oldFileId);
                    }
                }

                LoggerHelper::info('Village profile updated successfully', [
                    'village_profile_id' => $villageProfile?->id,
                    'name' => $villageProfile->name,
                ]);

                return $villageProfile->fresh('files');
            });

        } catch (\Throwable $th) {
            LoggerHelper::error('Failed to update village profile', [
                'village_profile_id' => $villageProfile?->id,
                'error_message' => $th->getMessage(),
            ]);

            throw $th;
        }
    }

    public function deleteVillageProfile(VillageProfile $villageProfile)
    {
        try {
            return DB::transaction(function () use ($villageProfile) {
                $fileIds = $villageProfile->files()->pluck('files.id')->toArray();

                foreach ($fileIds as $fileId) {
                    $this->fileService->deleteFile($fileId);
                }

                $villageProfile->villageProfileFiles()->delete();
                $villageProfile->delete();

                LoggerHelper::info('Village profile deleted successfully', [
                    'village_profile_id' => $villageProfile?->id,
                    'name' => $villageProfile->name,
                ]);

                return $villageProfile;
            });
        } catch (\Throwable $th) {
            LoggerHelper::error('Failed to delete village profile', [
                'village_profile_id' => $villageProfile?->id,
                'error_message' => $th->getMessage(),
            ]);

            throw $th;
        }
    }
}
