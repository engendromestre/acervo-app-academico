<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Rodar o seeder
    $this->seed(\Database\Seeders\BasicAdminPermissionSeeder::class);
    // Obter usuários criados pelo seeder
    $this->superAdmin = User::where('email', 'superadmin@engendrando.com')->first();
    $this->adminUser = User::where('email', 'admin@engendrando.com')->first();
    $this->normalUser = User::where('email', 'user@engendrando.com')->first();
});

it('can create user', function () {
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

it('normal user cannot create user', function () {
    // Atue como um usuário normal
    $this->actingAs($this->normalUser);

    // Tente acessar a rota de criação de usuários
    $response = $this->get(route('user.create'));

    // Verifique se o acesso é negado (deve redirecionar para a página de login, ou outra página devido à política de autorização)
    $response->assertStatus(403);

    // Tente enviar uma requisição POST para criar um usuário
    $response = $this->post(route('user.store'), [
        'name' => 'Novo Usuário',
        'email' => 'novousuario@example.com',
        'password' => 'password123',
        'role' => 2, // Id de um papel qualquer, apenas para simulação
    ]);

    // Verifique se a criação do usuário é negada (deve redirecionar ou retornar um erro de autorização)
    $response->assertStatus(403);
});

it('can update user', function () {
    $response = $this->actingAs($this->adminUser)->get(route('user.index'));
    $userData = [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => Hash::make('password'), // A senha será gerada pelo método do controller
        'role' => 'writer'
    ];
    $userStore = $this->post(route('user.store'), $userData);
    $userStore->assertRedirect(route('user.index'));

    $user = User::where('email', 'newuser@example.com')->first();
    $updateData = [
        'name' => 'Updated Name',
        'email' => 'updatedname@example.com',
        'role' => 'writer', // Exemplo de atribuição de papel
    ];
    $response = $this->put(route('user.update', $user), $updateData);
    $response->assertRedirect(route('user.index'));
    $this->assertDatabaseHas('users', ['name' => 'Updated Name']);
});

it('can delete a user', function () {
    $this->actingAs($this->superAdmin);

    $user = User::factory()->create();

    $response = $this->delete(route('user.destroy', $user));

    $response->assertRedirect(route('user.index'));
    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});


it('can list users with filters and pagination', function () {
    // Crie alguns usuários fictícios no banco de dados
    User::factory()->count(10)->create();

    // Atue como um usuário autenticado (no caso, superadmin)
    $this->actingAs($this->adminUser);

    // Acesse a rota que retorna a listagem de usuários com filtros e paginação
    $response = $this->get(route('user.index'));

    // Verifique se a resposta foi bem-sucedida (status HTTP 200)
    $response->assertStatus(200);

    // Verifique se a página renderizada é a correta (componente Inertia 'Admin/User/Index')
    $response->assertInertia(
        fn ($page) => $page
            ->component('Admin/User/Index')
            ->has('data')
            ->has('filters')
            ->has('roles')
            ->has('modelHasRoles')
            ->where('can.list', true)
    );

    // Verifique se os dados de usuário correspondem aos filtros aplicados
    $response->assertInertia(
        fn ($page) => $page
            ->component('Admin/User/Index')
            ->where('data', function ($users) {
                return count($users["data"]) === 8; // Verifique se estão sendo retornados 8 usuários por página
            })
    );

    // Verifique se os dados de usuário são corretamente filtrados por nome
    $response = $this->get(route('user.index', ['q' => 'User'])); // Filtrar por usuários com 'User' no nome

    $response->assertInertia(
        fn ($page) => $page
            ->where('data', function ($users) {
                return count($users["data"]) === 2; // Verifique se 3 usuários correspondem ao filtro 'User' no nome
            })
    );
});

it('requires name to be present', function () {
    $response = $this->actingAs($this->adminUser)->get(route('user.index'));
    $response = $this->post(route('user.store'), [
        'email' => 'test@example.com',
        'role' => 'admin',
    ]);
    $response->assertSessionHasErrors('name');
});

it('requires name to be unique', function () {
    $response = $this->actingAs($this->adminUser)->get(route('user.index'));

    User::factory()->create(['name' => 'Existing User']);

    $response = $this->post(route('user.store'), [
        'name' => 'Existing User',
        'email' => 'test@example.com',
        'role' => 'admin',
    ]);

    $response->assertSessionHasErrors('name');
});

it('requires name to have min length', function () {
    $response = $this->actingAs($this->adminUser)->get(route('user.index'));

    $response = $this->post(route('user.store'), [
        'name' => 'sh',
        'email' => 'test@example.com',
        'role' => 'admin',
    ]);
    $response->assertStatus(302);
    $response->assertSessionHasErrors('name');
});

it('requires name to have max length', function () {
    $response = $this->actingAs($this->adminUser)->get(route('user.index'));

    $response = $this->post(route('user.store'), [
        'name' => str_repeat('t', 51),
        'email' => 'test@example.com',
        'role' => 'admin',
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('name');
});

it('requires email to be present', function () {
    $response = $this->actingAs($this->adminUser)->get(route('user.index'));

    $response = $this->post(route('user.store'), [
        'name' => 'Test User',
        'role' => 'admin',
    ]);

    $response->assertSessionHasErrors('email');
});

it('requires email to be a valid email format', function () {
    $response = $this->actingAs($this->adminUser)->get(route('user.index'));

    $response = $this->post(route('user.store'), [
        'name' => 'Test User',
        'email' => 'invalid-email-format',
        'role' => 'admin',
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('email');
});

it('requires email to be unique', function () {
    $response = $this->actingAs($this->adminUser)->get(route('user.index'));

    User::factory()->create(['email' => 'existing@example.com']);

    $response = $this->post(route('user.store'), [
        'name' => 'Test User',
        'email' => 'existing@example.com',
        'role' => 'admin',
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('email');
});

it('requires email to have min length', function () {
    $response = $this->actingAs($this->adminUser)->get(route('user.index'));

    $response = $this->post(route('user.store'), [
        'name' => 'Test User',
        'email' => 'a@a.com',
        'role' => 'admin',
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('email');
});

it('requires email to have max length', function () {
    $response = $this->actingAs($this->adminUser)->get(route('user.index'));

    $response = $this->post(route('user.store'), [
        'name' => 'Test User',
        'email' => str_repeat('a', 151) . '@example.com',
        'role' => 'admin',
    ]);

    $response->assertSessionHasErrors('email');
});

it('requires role to be present', function () {
    $response = $this->actingAs($this->adminUser)->get(route('user.index'));

    $response = $this->post(route('user.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $response->assertSessionHasErrors('role');
});

it('requires role to be writer or admin', function () {
    $response = $this->actingAs($this->adminUser)->get(route('user.index'));

    $response = $this->post(route('user.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'role' => 'invalid_role',
    ]);

    $response->assertSessionHasErrors('role');

    $response = $this->post(route('user.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'role' => 'writer',
    ]);

    $response->assertSessionDoesntHaveErrors('role'); // No errors expected

    $response = $this->post(route('user.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'role' => 'admin',
    ]);

    $response->assertSessionDoesntHaveErrors('role'); // No errors expected
});
