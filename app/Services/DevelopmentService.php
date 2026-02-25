<?php

namespace App\Services;

use App\Repository\DevelopmentRepository;

class DevelopmentService
{
    private $developmentRepository;

    public function __construct(DevelopmentRepository $developmentRepository)
    {
        $this->developmentRepository = $developmentRepository;
    }
}