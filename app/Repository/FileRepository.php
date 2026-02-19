<?php

namespace App\Repository;

use App\Models\File;

class FileRepository
{
    public function __construct(private File $file) {}

    /**
     * Save file info ke database.
     *
     * @param  array  $fileData
     * @return File
     */
    public function save(array $fileData): File
    {
        return $this->file->create($fileData);
    }

    /**
     * Find file by ID.
     *
     * @param  int  $id
     * @return File|null
     */
    public function findById(int $id): ?File
    {
        return $this->file->find($id);
    }

    /**
     * Delete file from database.
     *
     * @param  File  $file
     * @return bool
     */
    public function delete(File $file): bool
    {
        return $file->delete();
    }
}