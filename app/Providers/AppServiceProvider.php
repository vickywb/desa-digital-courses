<?php

namespace App\Providers;

use App\Models\Development;
use App\Models\Event;
use App\Models\FamilyMember;
use App\Models\HeadOfFamily;
use App\Models\SocialAssistance;
use App\Models\VillageProfile;
use App\Policies\DevelopmentPolicy;
use App\Policies\EventPolicy;
use App\Policies\FamilyMemberPolicy;
use App\Policies\HeadOfFamilyPolicy;
use App\Policies\SocialAssistancePolicy;
use App\Policies\VillageProfilePolicy;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configureDefault();
        $this->registerPolicies();
    }

    protected function configureDefault(): void
    {
        Date::use(CarbonImmutable::class);
    }

    protected function registerPolicies(): void
    {
        Gate::policy(HeadOfFamily::class, HeadOfFamilyPolicy::class);
        Gate::policy(FamilyMember::class, FamilyMemberPolicy::class);
        Gate::policy(Event::class, EventPolicy::class);
        Gate::policy(SocialAssistance::class, SocialAssistancePolicy::class);
        Gate::policy(Development::class, DevelopmentPolicy::class);
        Gate::policy(VillageProfile::class, VillageProfilePolicy::class);
    }
}
