<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('rejects logout without token', function () {

    $response = $this->postJson('/api/v1/logout');

    $response->assertStatus(401);
});


