<?php

namespace App\Repository;

use App\Models\Development;

class DevelopmentRepository
{
    public function __construct(private Development $development) {}

    public function save(Development $development): Development
    {
        $development->save();
        return $development->fresh();

    }
}