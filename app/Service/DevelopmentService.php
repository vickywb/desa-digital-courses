<?php

namespace App\Service;

use App\Repository\DevelopmentRepository;

class DevelopmentService
{
    private $developmentRepository;

    public function __construct(DevelopmentRepository $developmentRepository)
    {
        $this->developmentRepository = $developmentRepository;
    }
}