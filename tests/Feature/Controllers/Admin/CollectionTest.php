<?php

use App\Models\User;
use App\Models\Collection;
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

it('can create collection', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('collection.index'));

    $data = [
        'name' => 'Graduation'
    ];

    $response = $this->post(route('collection.store'), $data);

    $response->assertRedirect(route('collection.index'));
    $this->assertDatabaseHas('collections', ['name' => 'Graduation']);
});

it('can update permission', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('collection.index'));

    // Crie uma permissão para testar a atualização
    $createData = Collection::create(['name' => 'Graduation']);

    $data = Collection::where('id',  $createData->id)->first();

    // Dados de atualização
    $updateData = [
        'name' => 'Postgraduate',
    ];

    $response = $this->put(route('collection.update', $data), $updateData);
    $response->assertRedirect(route('collection.index'));
    $this->assertDatabaseHas('collections', ['id' => $data->id, 'name' => 'Postgraduate']);
});

it('can delete a collection', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('collection.index'));

    $createData = Collection::create(['name' => 'Graduation']);

    $response = $this->delete(route('collection.destroy', $createData));

    $response->assertRedirect(route('collection.index'));
    $this->assertDatabaseMissing('collections', [
        'id' => $createData->id,
    ]);
});

it('can list collections with filters and pagination', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('collection.index'));

    $datas = [
        'Graduation',
        'Postgraduate',
        'Master'
    ];

    foreach ($datas as $collection) {
        Collection::create(['name' => $collection]);
    }

    $response = $this->get(route('collection.index'));
    $response->assertStatus(200);

    $response->assertInertia(
        fn ($page) => $page
            ->component('Admin/Collection/Index')
            ->has('fields')
            ->has('data')
            ->has('can')
    );

    $response->assertInertia(
        fn ($page) => $page
            ->component('Admin/Collection/Index')
            ->where('data', function ($datas) {
                return count($datas["data"]) === 3;
            })
    );

    $response = $this->get(route('collection.index', ['q' => 'gradua']));

    $response->assertInertia(
        fn ($page) => $page
            ->where('data', function ($datas) {
                return count($datas["data"]) === 2;
            })
    );
});

it('requires name to be present', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('collection.index'));
    $response = $this->post(route('collection.store'), []);
    $response->assertSessionHasErrors('name');
});

it('requires name to be unique', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('collection.index'));

    $data = [
        'Graduation'
    ];
    $response = $this->post(route('collection.store'), $data);

    $response = $this->post(route('collection.store'), [
        'Graduation'
    ]);

    $response->assertSessionHasErrors('name');
});

it('requires name to have max length', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('collection.index'));

    $response = $this->post(route('collection.store'), [
        'name' => str_repeat('t',51),
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('name');
});


