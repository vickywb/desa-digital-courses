<?php

use App\Http\Controllers\Api\V1\Admin\UserController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\FamilyMember\HeadOfFamilyController as MyFamilyMemberController;
use App\Http\Controllers\Api\V1\HeadVillage\DevelopmentApplicantController;
use App\Http\Controllers\Api\V1\HeadVillage\DevelopmentController;
use App\Http\Controllers\Api\V1\HeadVillage\EventController;
use App\Http\Controllers\Api\V1\HeadVillage\EventParticipantController;
use App\Http\Controllers\Api\V1\HeadVillage\FamilyMemberController;
use App\Http\Controllers\Api\V1\HeadVillage\HeadOfFamilyController;
use App\Http\Controllers\Api\V1\HeadVillage\SocialAssistanceController;
use App\Http\Controllers\Api\V1\HeadVillage\SocialAssistanceRecipientController;
use App\Http\Controllers\Api\V1\HeadVillage\VillageProfileController;
use Illuminate\Support\Facades\Route;

// PUBLIC ROUTES
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::post('register', [AuthController::class, 'register']);

// Auth routes (semua role) — protected
Route::middleware('auth:sanctum')
    ->prefix('auth')
    ->controller(AuthController::class)
    ->group(function () {
        Route::post('logout', 'logout');
        Route::get('me', 'me');
        Route::put('profile', 'updateProfile');
    });

// Admin routes
Route::middleware(['auth:sanctum', 'role:admin'])
    ->prefix('admin')
    ->group(function () {
        Route::apiResource('users', UserController::class)
            ->only(['index', 'show', 'update']);
    });

// Staff routes — index, show, store, update (no destroy)
Route::middleware(['auth:sanctum', 'role:admin,head_village,staff'])
    ->prefix('village-staff')
    ->group(function () {
        Route::apiResource('events', EventController::class)
            ->except(['destroy']);
        Route::apiResource('events.participants', EventParticipantController::class)
            ->only(['index', 'show', 'update']);

        Route::apiResource('developments', DevelopmentController::class)
            ->except(['destroy']);
        Route::apiResource('developments.applicants', DevelopmentApplicantController::class)
            ->only(['index', 'show']);

        Route::apiResource('social-assistances', SocialAssistanceController::class)
            ->except(['destroy']);
        Route::apiResource('social-assistances.recipients', SocialAssistanceRecipientController::class)
            ->only(['index', 'show', 'update']);

        Route::apiResource('head-families', HeadOfFamilyController::class)
            ->only(['index', 'show', 'update']);
        Route::apiResource('head-families.members', FamilyMemberController::class)
            ->except(['destroy']);

        Route::apiResource('village-profiles', VillageProfileController::class)
            ->only(['index', 'store', 'show', 'update']);
    });

// Destroy only — admin & head_village
Route::middleware(['auth:sanctum', 'role:admin,head_village'])
    ->prefix('village-staff')
    ->group(function () {
        Route::delete('events/{event}', [EventController::class, 'destroy']);
        Route::delete('developments/{development}', [DevelopmentController::class, 'destroy']);
        Route::delete('social-assistances/{socialAssistance}', [SocialAssistanceController::class, 'destroy']);
        Route::delete('head-families/{headFamily}/members/{member}', [FamilyMemberController::class, 'destroy']);
    });

// Family Member routes — untuk kepala keluarga
Route::middleware(['auth:sanctum', 'role:head_of_family'])
    ->prefix('village-resident')
    ->group(function () {
        Route::apiResource('events', EventController::class)
            ->only(['index', 'show']);
        Route::apiResource('events.participants', EventParticipantController::class)
            ->only(['store', 'destroy']);

        Route::apiResource('developments', DevelopmentController::class)
            ->only(['index', 'show']);
        Route::apiResource('developments.applicants', DevelopmentApplicantController::class)
            ->only(['store', 'destroy']);

        Route::apiResource('social-assistances', SocialAssistanceController::class)
            ->only(['index', 'show']);
        Route::apiResource('social-assistances.recipients', SocialAssistanceRecipientController::class)
            ->only(['store', 'destroy']);

        Route::apiResource('family-members', MyFamilyMemberController::class);
    });
