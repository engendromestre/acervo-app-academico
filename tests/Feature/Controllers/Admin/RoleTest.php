<?php
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Rodar o seeder
    $this->seed(\Database\Seeders\BasicAdminPermissionSeeder::class);
    // Obter usuários criados pelo seeder
    $this->superAdmin = User::where('email', 'superadmin@engendrando.com')->first();
    $this->adminUser = User::where('email', 'admin@engendrando.com')->first();
    $this->normalUser = User::where('email', 'user@engendrando.com')->first();
});


it('can create role', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('user.index'));
    $userData = [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => Hash::make('password'), // A senha será gerada pelo método do controller
        'role' => 'writer'
    ];
    $response = $this->post(route('user.store'), $userData);
    $response->assertRedirect(route('user.index'));
    $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
});
