<?php

use App\Models\User;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Rodar o seeder
    $this->seed(\Database\Seeders\BasicAdminPermissionSeeder::class);
    // Obter usuários criados pelo seeder
    $this->superAdmin = User::where('email', env('APP_SUPERADMIN'))->first();
    $this->adminUser = User::where('email', env('APP_ADMIN'))->first();
    $this->normalUser = User::where('email', env('APP_USER'))->first();
});


it('can create permission', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('permission.index'));

    $permissionData = [
        'permission list',
    ];

    $response = $this->post(route('permission.store'), $permissionData);

    $response->assertRedirect(route('permission.index'));
    $this->assertDatabaseHas('permissions', ['name' => 'permission list']);
});

it('normal user cannot create permission', function () {
    $response = $this->actingAs($this->normalUser)->get(route('permission.index'));

    $permissionData = [
        'permission list',
    ];

    $response = $this->post(route('permission.store'), $permissionData);

    // Verifique se a criação do usuário é negada (deve redirecionar ou retornar um erro de autorização)
    $response->assertStatus(403);
});

it('can update permission', function () {
    $response = $this->actingAs($this->adminUser)->get(route('permission.index'));

    // Crie uma permissão para testar a atualização
    $permission = Permission::create(['name' => 'edit articles']);

    $permission = Permission::where('name', 'edit articles')->first();

    // Dados de atualização
    $updateData = [
        'name' => 'create articles',
    ];
    $response = $this->put(route('permission.update', $permission), $updateData);
    $response->assertRedirect(route('permission.index'));
    // Verifique se a permissão foi atualizada no banco de dados
    $this->assertDatabaseHas('permissions', ['id' => $permission->id, 'name' => 'create articles']);
});

it('can delete a permission', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('permission.index'));

    $permission = Permission::create(['name' => 'edit articles']);

    $response = $this->delete(route('permission.destroy', $permission));

    $response->assertRedirect(route('permission.index'));
    $this->assertDatabaseMissing('permissions', [
        'id' => $permission->id,
    ]);
});

it('can list permissions with filters and pagination', function () {
    $response = $this->actingAs($this->adminUser)->get(route('permission.index'));

    // create permissions
    $permissions = [
        'article list',
        'article create',
        'article edit',
        'article delete',
    ];

    foreach ($permissions as $permission) {
        Permission::create(['name' => $permission]);
    }

    $response = $this->get(route('permission.index'));
    $response->assertStatus(200);

    $response->assertInertia(
        fn ($page) => $page
            ->component('Admin/Permission/Index')
            ->has('fields')
            ->has('filters')
            ->has('data')
            ->has('can')
    );

    $response->assertInertia(
        fn ($page) => $page
            ->component('Admin/Permission/Index')
            ->where('data', function ($permissions) {
                return count($permissions["data"]) === 8; // Verifique se estão sendo retornados 8 permissoes por página
            })
    );

    $response = $this->get(route('permission.index', ['q' => 'article'])); // Filtrar por permissoes com 'article' no nome

    $response->assertInertia(
        fn ($page) => $page
            ->where('data', function ($roles) {
                return count($roles["data"]) === 4;
            })
    );
});

it('requires name to be present', function () {
    $response = $this->actingAs($this->adminUser)->get(route('permission.index'));
    $response = $this->post(route('permission.store'), []);
    $response->assertSessionHasErrors('name');
});

it('requires name to be unique', function () {
    $response = $this->actingAs($this->adminUser)->get(route('permission.index'));

    $permissionDataAdmin = [
        'article list'
    ];
    $response = $this->post(route('permission.store'), $permissionDataAdmin);

    $response = $this->post(route('permission.store'), [
        'article list'
    ]);

    $response->assertSessionHasErrors('name');
});

it('requires name to have max length', function () {
    $response = $this->actingAs($this->adminUser)->get(route('permission.index'));

    $response = $this->post(route('permission.store'), [
        'name' => str_repeat('t',51),
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('name');
});
