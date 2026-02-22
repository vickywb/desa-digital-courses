<?php
// tests/Feature/Auth/RegisterTest.php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ✅ Helper function - reusable payload
function validRegisterPayload(array $override = []): array
{
    return array_merge([
        'username'              => 'johndoe',
        'full_name'                  => 'John Doe',
        'email'                 => 'john@example.com',
        'password'              => 'Password123!',
        'password_confirmation' => 'Password123!',
        'identity_number'       => '1234567890123456',
        'gender'                => 'male',
        'date_of_birth'         => '1990-01-01',
        'occupation'            => 'Developer',
        'marital_status'        => 'single',
        'phone_number'          => '081234567890',
    ], $override);
}

// ==========================================
// ✅ SUCCESS CASES
// ==========================================

it('can register without image', function () {
    $response = $this->postJson('/api/v1/auth/register', validRegisterPayload());
    
    $response->assertStatus(201)
             ->assertJsonStructure([
                 'status',
                 'message',
                 'data' => [
                     'id',
                     'username',
                     'email',
                     'role',
                     'head_of_family' => [ // ⭐ Sesuaikan dengan response
                         'id',
                         'full_name',
                         'identity_number',
                         'gender',
                         'file' // ⭐ null jika tidak ada image
                     ]
                 ],
             ]);
    
    $this->assertDatabaseHas('users', [
        'username' => 'johndoe',
        'email' => 'john@example.com',
    ]);
    
    $this->assertDatabaseHas('head_of_families', [
        'identity_number' => '1234567890123456',
        'file_id' => null, // ⭐ Karena tanpa image
    ]);
});

it('can register with image', function () {
    Storage::fake('public');
    
    $response = $this->postJson('/api/v1/auth/register', validRegisterPayload([
        'image' => UploadedFile::fake()->image('profile.jpg')
    ]));
    
    $response->assertStatus(201)
             ->assertJsonStructure([
                 'status',
                 'message',
                 'data' => [
                     'id',
                     'username',
                     'email',
                     'head_of_family' => [
                         'file' // ⭐ Pastikan file relationship ada
                     ]
                 ]
             ]);
    
    // Cek database
    $this->assertDatabaseCount('files', 1);
    
    $file = \App\Models\File::first();
    $this->assertNotNull($file);
    
    // Cek file benar-benar ter-upload
    Storage::disk('public')->assertExists($file->file_path);
    
    // Cek head_of_family punya file_id
    $this->assertDatabaseHas('head_of_families', [
        'identity_number' => '1234567890123456',
        'file_id' => $file->id,
    ]);
});;

// ==========================================
// ❌ VALIDATION CASES
// ==========================================

it('fails without required fields', function () {
    $this->postJson('/api/v1/auth/register', [])
         ->assertStatus(422)
         ->assertJsonValidationErrors([
             'username',
             'full_name',
             'email',
             'password',
             'identity_number',
             'gender',
             'date_of_birth',
             'occupation',
             'marital_status',
         ]);
});

it('fails with duplicate email', function () {
    User::factory()->create(['email' => 'john@example.com']);

    $this->postJson('/api/v1/auth/register', validRegisterPayload())
         ->assertStatus(422)
         ->assertJsonValidationErrors(['email']);
});

it('fails with duplicate username', function () {
    User::factory()->create(['username' => 'johndoe']);

    $this->postJson('/api/v1/auth/register', validRegisterPayload())
         ->assertStatus(422)
         ->assertJsonValidationErrors(['username']);
});

it('fails with weak password', function () {
    $this->postJson('/api/v1/auth/register', validRegisterPayload([
        'password'              => 'Secret123',
        'password_confirmation' => 'Secret123',
    ]))
    ->assertStatus(422)
    ->assertJsonValidationErrors(['password']);
});

it('fails with password mismatch', function () {
    $this->postJson('/api/v1/auth/register', validRegisterPayload([
        'password_confirmation' => 'WrongPassword123!',
    ]))
    ->assertStatus(422)
    ->assertJsonValidationErrors(['password']);
});

it('fails with invalid image type', function () {
    Storage::fake('public');

    $this->postJson('/api/v1/auth/register', validRegisterPayload([
        'image' => UploadedFile::fake()->create('document.pdf', 100)
    ]))
    ->assertStatus(422)
    ->assertJsonValidationErrors(['image']);
});

it('fails with image too large', function () {
    Storage::fake('public');

    $this->postJson('/api/v1/auth/register', validRegisterPayload([
        'image' => UploadedFile::fake()->image('profile.jpg')->size(3000) // 3MB
    ]))
    ->assertStatus(422)
    ->assertJsonValidationErrors(['image']);
});

it('fails with future date of birth', function () {
    $this->postJson('/api/v1/auth/register', validRegisterPayload([
        'date_of_birth' => now()->addYear()->format('Y-m-d')
    ]))
    ->assertStatus(422)
    ->assertJsonValidationErrors(['date_of_birth']);
});

it('fails with invalid marital status', function () {
    $this->postJson('/api/v1/auth/register', validRegisterPayload([
        'marital_status' => 'invalid_status'
    ]))
    ->assertStatus(422)
    ->assertJsonValidationErrors(['marital_status']);
});

it('fails with invalid phone number format', function () {
    $this->postJson('/api/v1/auth/register', validRegisterPayload([
        'phone_number' => 'invalid-phone'
    ]))
    ->assertStatus(422)
    ->assertJsonValidationErrors(['phone_number']);
});