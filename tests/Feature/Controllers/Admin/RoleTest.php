<?php

use App\Models\User;
use App\Models\Permission;
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

    //Obter uma lista de Permissões
    $this->permissions = Permission::factory()->count(10)->create();
});


it('can create role', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('role.index'));

    $roleData = [
        'name' => 'admin',
        'permissions' => $this->permissions
    ];

    $response = $this->post(route('role.store'), $roleData);

    $response->assertRedirect(route('role.index'));
    $this->assertDatabaseHas('roles', ['name' => 'admin']);
});


it('normal user cannot create role', function () {
    // Atue como um usuário normal
    $this->actingAs($this->normalUser);

    // Tente acessar a rota de criação de usuários
    $response = $this->get(route('role.create'));

    $response->assertStatus(403);

    // Tente enviar uma requisição POST para criar um usuário
    $response = $this->post(route('role.store'), [
        'name' => 'new role',
        'permissions' => $this->permissions
    ]);

    // Verifique se a criação do usuário é negada (deve redirecionar ou retornar um erro de autorização)
    $response->assertStatus(403);
});

it('can update role', function () {
    $response = $this->actingAs($this->adminUser)->get(route('role.index'));
    $roleData = [
        'name' => 'admin',
        'permissions' => $this->permissions
    ];
    $roleStore = $this->post(route('role.store'), $roleData);
    $roleStore->assertRedirect(route('role.index'));

    $role = Role::where('name', 'admin')->first();
    $updateData = [
        'name' => 'writer',
        'permissions' => $this->permissions
    ];
    $response = $this->put(route('role.update', $role), $updateData);
    $response->assertRedirect(route('role.index'));
    $this->assertDatabaseHas('roles', ['name' => 'writer']);
});

it('can delete a role', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('role.index'));

    $roleData = [
        'name' => 'admin',
        'permissions' => $this->permissions
    ];
   $this->post(route('role.store'), $roleData);

   $role = Role::where('name','admin')->first();

    $response = $this->delete(route('role.destroy', $role));

    $response->assertRedirect(route('role.index'));
    $this->assertDatabaseMissing('roles', [
        'id' => $role->id,
    ]);
});

it('can list roles with filters and pagination', function () {
    $response = $this->actingAs($this->adminUser)->get(route('role.index'));
    $roleDataAdmin = [
        'name' => 'admin',
        'permissions' => $this->permissions
    ];
    $roleStore = $this->post(route('role.store'), $roleDataAdmin);
    $roleStore->assertRedirect(route('role.index'));
    $roleDataWriter = [
        'name' => 'admin',
        'permissions' =>$this->permissions
    ];
    $roleStore = $this->post(route('role.store'), $roleDataWriter);
    $roleStore->assertRedirect(route('role.index'));

    $response = $this->get(route('role.index'));
    $response->assertStatus(200);

    $response->assertInertia(
        fn ($page) => $page
            ->component('Admin/Role/Index')
            ->has('data')
            ->has('fields')
            ->has('filters')
            ->has('permissions')
            ->has('roleHasPermissions')
    );

    $response->assertInertia(
        fn ($page) => $page
            ->component('Admin/Role/Index')
            ->where('data', function ($roles) {
                return count($roles["data"]) === 2; // Verifique se estão sendo retornados 8 roles por página
            })
    );

    $response = $this->get(route('role.index', ['q' => 'admin'])); // Filtrar por Roles com 'admin' no nome

    $response->assertInertia(
        fn ($page) => $page
            ->where('data', function ($roles) {
                return count($roles["data"]) === 1; // Verifique se 3 usuários correspondem ao filtro 'User' no nome
            })
    );
});

it('requires name to be present', function () {
    $response = $this->actingAs($this->adminUser)->get(route('role.index'));
    $response = $this->post(route('role.store'), [
        'permissions' =>$this->permissions
    ]);
    $response->assertSessionHasErrors('name');
});

it('requires name to be unique', function () {
    $response = $this->actingAs($this->adminUser)->get(route('role.index'));

    $roleDataAdmin = [
        'name' => 'admin',
        'permissions' => $this->permissions
    ];
    $response = $this->post(route('role.store'), $roleDataAdmin);

    $response = $this->post(route('role.store'), [
        'name' => 'admin',
        'permissions' => $this->permissions
    ]);

    $response->assertSessionHasErrors('name');
});

it('requires name to have max length', function () {
    $response = $this->actingAs($this->adminUser)->get(route('role.index'));

    $response = $this->post(route('role.store'), [
        'name' => str_repeat('t',51),
        'permissions' => $this->permissions
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('name');
});
