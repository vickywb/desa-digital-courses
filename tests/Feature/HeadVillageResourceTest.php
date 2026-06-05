<?php

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\FamilyMember;
use App\Models\HeadOfFamily;
use App\Models\SocialAssistance;
use App\Models\SocialAssistanceRecipient;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->staffUser = User::factory()->create(['role' => 'head_village']);
    $this->headOfFamilyUser = User::factory()->create(['role' => 'head_of_family']);
    $this->headOfFamily = HeadOfFamily::factory()->create([
        'user_id' => $this->headOfFamilyUser->id,
        'marital_status' => 'single',
        'gender' => 'male',
    ]);
});

test('head village can update event participant', function () {
    $event = Event::create([
        'title' => 'Gotong Royong',
        'description' => 'Kegiatan bersih-bersih desa',
        'price' => '0',
        'start_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
        'is_active' => true,
    ]);

    $familyMember = FamilyMember::create([
        'head_of_family_id' => $this->headOfFamily->id,
        'full_name' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'identity_number' => '1234567890123456',
        'phone_number' => '081234567890',
        'occupation' => 'Petani',
        'date_of_birth' => '1990-01-01',
        'gender' => 'male',
        'marital_status' => 'single',
        'relation' => 'child',
    ]);

    $participant = EventParticipant::create([
        'event_id' => $event->id,
        'head_of_family_id' => $this->headOfFamily->id,
        'family_member_id' => $familyMember->id,
        'quantity' => 1,
        'total_price' => '0',
        'payment_status' => 'pending',
    ]);

    $response = actingAs($this->staffUser, 'sanctum')
        ->putJson(route('events.participants.update', [$event, $participant]), [
            'payment_status' => 'paid',
            'quantity' => 2,
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.payment_status', 'paid')
        ->assertJsonPath('data.quantity', 2);

    expect($participant->fresh()->payment_status)->toBe('paid');
});

test('head village can update social assistance recipient', function () {
    $socialAssistance = SocialAssistance::create([
        'title' => 'Bantuan Pangan',
        'category' => 'staple',
        'amount' => '500000',
        'provider' => 'Pemdes',
        'description' => 'Bantuan untuk keluarga miskin',
        'is_available' => true,
    ]);

    $recipient = SocialAssistanceRecipient::create([
        'social_assistance_id' => $socialAssistance->id,
        'head_of_family_id' => $this->headOfFamily->id,
        'bank' => 'BCA',
        'amount' => '500000',
        'account_number' => '1234567890',
        'reason' => 'Keluarga kurang mampu',
        'proof' => 'proof-file.jpg',
        'status' => 'pending',
    ]);

    $response = actingAs($this->staffUser, 'sanctum')
        ->putJson(route('social-assistances.recipients.update', [$socialAssistance, $recipient]), [
            'status' => 'approved',
            'amount' => '600000',
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.status', 'approved')
        ->assertJsonPath('data.amount', '600000');

    expect($recipient->fresh()->status)->toBe('approved');
});

test('head of family can register for an event participant', function () {
    $event = Event::create([
        'title' => 'Gotong Royong',
        'description' => 'Kegiatan bersih-bersih desa',
        'price' => '0',
        'start_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
        'is_active' => true,
    ]);

    $member = FamilyMember::create([
        'head_of_family_id' => $this->headOfFamily->id,
        'full_name' => 'Siti Aminah',
        'email' => 'siti@example.com',
        'identity_number' => '9876543210987654',
        'phone_number' => '081234567891',
        'occupation' => 'Ibu Rumah Tangga',
        'date_of_birth' => '1992-05-05',
        'gender' => 'female',
        'marital_status' => 'married',
        'relation' => 'wife',
    ]);

    $response = actingAs($this->headOfFamilyUser, 'sanctum')
        ->postJson(route('events.participants.store', [$event]), [
            'family_member_id' => $member->id,
            'quantity' => 1,
            'total_price' => '0',
            'payment_status' => 'pending',
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.payment_status', 'pending')
        ->assertJsonPath('data.quantity', 1);

    expect(
        App\Models\EventParticipant::where('event_id', $event->id)
            ->where('head_of_family_id', $this->headOfFamily->id)
            ->exists()
    )->toBeTrue();
});

test('head of family can register for social assistance recipient', function () {
    $socialAssistance = SocialAssistance::create([
        'title' => 'Bantuan Pangan',
        'category' => 'staple',
        'amount' => '500000',
        'provider' => 'Pemdes',
        'description' => 'Bantuan untuk keluarga miskin',
        'is_available' => true,
    ]);

    $response = actingAs($this->headOfFamilyUser, 'sanctum')
        ->postJson(route('social-assistances.recipients.store', [$socialAssistance]), [
            'bank' => 'BCA',
            'amount' => '500000',
            'account_number' => '12345678901',
            'reason' => 'Keluarga kurang mampu',
            'proof' => 'proof-file.jpg',
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.status', 'pending')
        ->assertJsonPath('data.bank', 'BCA');

    expect(
        App\Models\SocialAssistanceRecipient::where('social_assistance_id', $socialAssistance->id)
            ->where('head_of_family_id', $this->headOfFamily->id)
            ->exists()
    )->toBeTrue();
});

test('head village can update head of family data', function () {
    $response = actingAs($this->staffUser, 'sanctum')
        ->putJson(route('head-families.update', [$this->headOfFamily]), [
            'phone_number' => '089876543210',
            'occupation' => 'Wiraswasta',
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.phone_number', '089876543210')
        ->assertJsonPath('data.occupation', 'Wiraswasta');

    expect($this->headOfFamily->fresh()->phone_number)->toBe('089876543210');
});

test('head village can update family member data', function () {
    $member = FamilyMember::create([
        'head_of_family_id' => $this->headOfFamily->id,
        'full_name' => 'Siti Aminah',
        'email' => 'siti@example.com',
        'identity_number' => '9876543210987654',
        'phone_number' => '081234567891',
        'occupation' => 'Ibu Rumah Tangga',
        'date_of_birth' => '1992-05-05',
        'gender' => 'female',
        'marital_status' => 'married',
        'relation' => 'wife',
    ]);

    $response = actingAs($this->staffUser, 'sanctum')
        ->putJson(route('head-families.members.update', [$this->headOfFamily, $member]), [
            'occupation' => 'Pengusaha',
            'phone_number' => '089012345678',
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.occupation', 'Pengusaha')
        ->assertJsonPath('data.phone_number', '089012345678');

    expect($member->fresh()->occupation)->toBe('Pengusaha');
});
