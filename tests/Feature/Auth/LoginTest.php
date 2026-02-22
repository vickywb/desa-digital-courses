<?php

// tests/Feature/Auth/LoginTest.php

use App\Models\HeadOfFamily;
use App\Models\User;
use function Pest\Laravel\postJson;
use Illuminate\Support\Facades\Hash;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// Helper function untuk create user dengan password yang diketahui
function createUserWithPassword(string $password = 'Password123!'): User
{
    $user = User::factory()->create([
        'username' => 'johndoe',
        'email' => 'john@example.com',
        'password' => Hash::make($password),
    ]);

    HeadOfFamily::factory()->create([
        'user_id' => $user->id,
        'full_name' => 'John Doe',
        'identity_number' => '1234567890123456',
        'gender' => 'male',
        'date_of_birth' => '1990-01-01',
        'occupation' => 'Developer',
        'marital_status' => 'single',
        'phone_number' => '081234567890',
    ]);
    
    return $user->fresh('headOfFamily'); 
}

// ==========================================
// ✅ SUCCESS CASES
// ==========================================

it('can login with valid email and password', function () {
    $user = createUserWithPassword('Password123!');
    
    $response = postJson('/api/v1/auth/login', [
        'email' => 'john@example.com',
        'password' => 'Password123!',
    ]);
    
    $response->assertStatus(200)
             ->assertJsonStructure([
                 'status',
                 'message',
                 'data' => [
                     'user' => [
                         'username',
                         'email',
                         'role',
                     ],
                     'token', // Jika pakai Sanctum/Passport
                 ],
             ]);
});

it('can login with valid username and password', function () {
    $user = createUserWithPassword('Password123!');
    
    $response = postJson('/api/v1/auth/login', [
        'username' => 'johndoe', // Login pakai username
        'password' => 'Password123!',
    ]);
    
    $response->assertStatus(200)
             ->assertJsonStructure([
                 'status',
                 'message',
                 'data' => [
                     'user',
                     'token',
                 ],
             ]);
});

it('returns user data with head of family on login', function () {
    $user = createUserWithPassword('Password123!');
    
    $response = postJson('/api/v1/auth/login', [
        'email' => 'john@example.com',
        'password' => 'Password123!',
    ]);
    
    $response->assertStatus(200)
             ->assertJson([
                 'status' => 'success',
                 'data' => [
                     'user' => [
                         'id' => $user->id,
                         'username' => 'johndoe',
                         'email' => 'john@example.com',
                     ],
                 ],
             ]);
});

// ==========================================
// ❌ FAIL CASES - Invalid Credentials
// ==========================================

it('fails with invalid email', function () {
    createUserWithPassword('Password123!');
    
    $response = postJson('/api/v1/auth/login', [
        'email' => 'wrong@example.com',
        'password' => 'Password123!',
    ]);
    
    $response->assertStatus(401) // Unauthorized
             ->assertJson([
                 'status' => 'error',
                 'message' => 'Invalid credentials.',
             ]);
});

it('fails with invalid password', function () {
    createUserWithPassword('Password123!');
    
    $response = postJson('/api/v1/auth/login', [
        'email' => 'john@example.com',
        'password' => 'WrongPassword!',
    ]);
    
    $response->assertStatus(401)
             ->assertJson([
                 'status' => 'error',
                 'message' => 'Invalid credentials.',
             ]);
});

it('fails with non-existent user', function () {
    $response = postJson('/api/v1/auth/login', [
        'email' => 'notexist@example.com',
        'password' => 'Password123!',
    ]);
    
    $response->assertStatus(401)
             ->assertJson([
                 'status' => 'error',
                 'message' => 'Invalid credentials.',
             ]);
});

// ==========================================
// ❌ FAIL CASES - Validation
// ==========================================

it('fails without email or username', function () {
    $response = postJson('/api/v1/auth/login', [
        'password' => 'Password123!',
    ]);
    
    $response->assertStatus(422)
             ->assertJsonValidationErrors(['email']); // atau 'login' jika pakai field kombinasi
});

it('fails without password', function () {
    $response = postJson('/api/v1/auth/login', [
        'email' => 'john@example.com',
    ]);
    
    $response->assertStatus(422)
             ->assertJsonValidationErrors(['password']);
});

it('fails with invalid email format', function () {
    $response = postJson('/api/v1/auth/login', [
        'email' => 'not-an-email',
        'password' => 'Password123!',
    ]);
    
    $response->assertStatus(422)
             ->assertJsonValidationErrors(['email']);
});

it('fails with empty credentials', function () {
    $response = postJson('/api/v1/auth/login', []);
    
    $response->assertStatus(422)
             ->assertJsonValidationErrors(['email', 'password']);
});

// ==========================================
// 🔒 SECURITY CASES
// ==========================================

it('does not reveal whether email exists', function () {
    // Test dengan email yang ada
    createUserWithPassword('Password123!');
    $response1 = postJson('/api/v1/auth/login', [
        'email' => 'john@example.com',
        'password' => 'WrongPassword!',
    ]);
    
    // Test dengan email yang tidak ada
    $response2 = postJson('/api/v1/auth/login', [
        'email' => 'notexist@example.com',
        'password' => 'WrongPassword!',
    ]);
    
    // Kedua response harus sama (generic error message)
    expect($response1->json('message'))->toBe($response2->json('message'));
    expect($response1->status())->toBe($response2->status());
});

// ==========================================
// 🎫 TOKEN CASES (Jika pakai Sanctum)
// ==========================================

it('generates valid authentication token', function () {
    $user = createUserWithPassword('Password123!');
    
    $response = postJson('/api/v1/auth/login', [
        'email' => 'john@example.com',
        'password' => 'Password123!',
    ]);
    
    $response->assertStatus(200);
    
    $token = $response->json('data.token');
    
    expect($token)->not->toBeNull();
    expect($token)->toBeString();
    
    // Verify token works
    $this->assertDatabaseHas('personal_access_tokens', [
        'tokenable_id' => $user->id,
        'tokenable_type' => User::class,
    ]);
});

it('can access protected route with token', function () {
    $user = createUserWithPassword('Password123!');
    
    $loginResponse = postJson('/api/v1/auth/login', [
        'email' => 'john@example.com',
        'password' => 'Password123!',
    ]);
    
    $token = $loginResponse->json('data.token');
    
    // Try to access protected route
    $response = $this->withHeader('Authorization', "Bearer $token")
                     ->getJson('/api/v1/auth/me');
    
    $response->assertStatus(200)
             ->assertJson([
                 'data' => [
                     'user' => [
                         'id' => $user->id,
                         'email' => 'john@example.com',
                     ],
                 ],
             ]);
});