<?php

namespace App\Repository;

use App\Models\DevelopmentApplicant;

class DevelopmentApplicantRepository
{
    private $developmentApplicant;

    public function __construct(DevelopmentApplicant $developmentApplicant) {
        $this->developmentApplicant = $developmentApplicant;
    }

    public function save(DevelopmentApplicant $developmentApplicant)
    {
        $developmentApplicant->save();

        return $developmentApplicant;
    }
}