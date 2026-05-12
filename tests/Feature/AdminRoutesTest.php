<?php

use App\Models\User;

test('guest cannot access admin routes', function () {
    $response = $this->actingAsGuest()
        ->get('/admin/users');

    $response->assertStatus(404);
});

test('regular user cannot access admin routes', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->get('/admin/users');

    $response->assertStatus(404);
});

test('admin can access admin routes', function () {
    $admin = User::factory()
        ->state(['is_admin' => true])
        ->create();
    
    $response = $this->actingAs($admin)
        ->get('/admin/users');

    $response->assertStatus(200);
});
