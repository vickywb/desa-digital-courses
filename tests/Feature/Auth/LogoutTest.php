<?php

// tests/Feature/Auth/LogoutTest.php

use App\Models\User;
use function Pest\Laravel\{postJson, actingAs};
use Illuminate\Support\Facades\Auth;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ==========================================
// HELPER
// ==========================================

function loginAndGetToken(): array
{
    $user = createUserWithPassword('Password123!');
    
    $response = postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'Password123!',
    ]);
    
    return [
        'user' => $user,
        'token' => $response->json('data.token'),
    ];
}

// ==========================================
// ✅ SUCCESS CASES
// ==========================================

it('can logout successfully', function () {
    ['user' => $user] = loginAndGetToken();
    
    $response = $this->actingAs($user, 'sanctum')
                     ->postJson('/api/v1/auth/logout');
    
    $response->assertStatus(200)
             ->assertJson([
                 'status' => 'success',
                 'message' => 'Successfully logged out.',
             ]);
});

it('deletes token after logout', function () {
    ['user' => $user, 'token' => $token] = loginAndGetToken();

    $this->assertDatabaseHas('personal_access_tokens', [
        'tokenable_id' => $user->id,
    ]);

    $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/api/v1/auth/logout')
        ->assertStatus(200);

    $this->assertDatabaseMissing('personal_access_tokens', [
        'tokenable_id' => $user->id,
    ]);
});

it('cannot access protected routes after logout', function () {
    ['user' => $user, 'token' => $token] = loginAndGetToken();

    // 1. Logout dengan token valid
    $this->withHeader('Authorization', 'Bearer ' . $token)
         ->postJson('/api/v1/auth/logout')
         ->assertStatus(200);

    // 2. Hapus Session & State Auth
    $this->flushSession(); // Tambahkan ini untuk membuang cookies session
    Auth::forgetGuards();

    // 3. Akses route 'me' secara anonim
    // Gunakan withHeaders([]) untuk memastikan tidak ada header sisa
    $response = $this->withHeaders([])->getJson('/api/v1/auth/me');

    // 4. Harusnya sekarang dapet 401
    $response->assertStatus(401);
});

// ==========================================
// ❌ FAIL CASES
// ==========================================

it('fails to logout without authentication', function () {
    $response = postJson('/api/v1/auth/logout');
    
    $response->assertStatus(401)
             ->assertJson([
                 'message' => 'Unauthenticated.',
             ]);
});

it('fails to logout with invalid token', function () {
    $response = $this->withHeader('Authorization', 'Bearer invalid-token-xyz')
                     ->postJson('/api/v1/auth/logout');
    
    $response->assertStatus(401);
});