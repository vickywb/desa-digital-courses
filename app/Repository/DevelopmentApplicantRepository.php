<?php

namespace App\Repository;

use App\Models\DevelopmentApplicant;

class DevelopmentApplicantRepository
{
    public function __construct(private DevelopmentApplicant $developmentApplicant) {}

    public function save(DevelopmentApplicant $developmentApplicant): DevelopmentApplicant
    {
        $developmentApplicant->save();

        return $developmentApplicant->fresh();
    }
}