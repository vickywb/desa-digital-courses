<?php

namespace App\Repository;

use App\Models\Profile;

class ProfileRepository
{
    private $profile;

    public function __construct(Profile $profile) {
        $this->profile = $profile;
    }

    public function save(Profile $profile)
    {
        $profile->save();

        return $profile;
    }
}