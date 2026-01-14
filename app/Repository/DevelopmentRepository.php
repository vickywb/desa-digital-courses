<?php

namespace App\Repository;

use App\Models\Development;

class DevelopmentRepository
{
    private $development;

    public function __construct(Development $development) {
        $this->development = $development;
    }

    public function save(Development $development)
    {
        $development->save();

        return $development;
    }
}