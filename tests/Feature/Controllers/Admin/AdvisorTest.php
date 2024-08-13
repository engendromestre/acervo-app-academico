<?php

use App\Models\User;
use App\Models\Advisor;
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

it('can create advisor', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('advisor.index'));

    $data = [
        'name' => 'User advisor',
    ];

    $response = $this->post(route('advisor.store'), $data);

    $response->assertRedirect(route('advisor.index'));
    $this->assertDatabaseHas('advisors', ['name' => 'User advisor']);
});

it('can create advisor with bio', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('advisor.index'));

    $data = [
        'name' => 'User advisor',
        'bio' => 'Bio advisor',
    ];

    $response = $this->post(route('advisor.store'), $data);

    $response->assertRedirect(route('advisor.index'));
    $this->assertDatabaseHas('advisors', ['name' => 'User advisor']);
});

it('does not allow advisor creation with bio longer than 255 characters', function () {
    // Cria uma biografia com mais de 255 caracteres
    $longBio = str_repeat('a', 256); 

    $data = [
        'name' => 'User advisor',
        'bio' => $longBio,
    ];

    // Faz a requisição com a biografia longa
    $response = $this->actingAs($this->superAdmin)->post(route('advisor.store'), $data);

    // Verifica que a resposta é uma redireção, indicando que o processamento falhou
    $response->assertSessionHasErrors('bio');

    // Opcional: Verifique se o autor não foi salvo no banco de dados
    $this->assertDatabaseMissing('advisors', ['name' => 'User advisor', 'bio' => $longBio]);
});

it('can update advisor', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('advisor.index'));

    // Crie um registro para testar a atualização
    $createData = advisor::create(['name' => 'advisor Name']);

    $data = advisor::where('id',  $createData->id)->first();

    // Dados de atualização
    $updateData = [
        'name' => 'New advisor Name',
    ];

    $response = $this->put(route('advisor.update', $data), $updateData);
    $response->assertRedirect(route('advisor.index'));
    $this->assertDatabaseHas('advisors', ['id' => $data->id, 'name' => 'New advisor Name']);
});

it('does not allow updating advisor with name longer than 255 characters', function () {
    // Crie um registro para testar a atualização
    $createData = advisor::create(['name' => 'advisor Name']);

    // Crie um nome com mais de 255 caracteres
    $longName = str_repeat('a', 256);

    // Dados de atualização
    $updateData = [
        'name' => $longName,
    ];

    // Tenta atualizar o autor com um nome longo
    $response = $this->actingAs($this->superAdmin)->put(route('advisor.update', $createData->id), $updateData);

    // Verifica que a resposta contém erros de validação para o campo 'name'
    $response->assertSessionHasErrors('name');

    // Opcional: Verifique se o nome não foi alterado no banco de dados
    $this->assertDatabaseHas('advisors', ['id' => $createData->id, 'name' => 'advisor Name']);
});

it('can delete a advisor', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('advisor.index'));

    $createData = advisor::create(['name' => 'advisor Name']);

    $response = $this->delete(route('advisor.destroy', $createData));

    $response->assertRedirect(route('advisor.index'));
    $this->assertDatabaseMissing('advisors', [
        'id' => $createData->id,
    ]);
});

it('does not allow unadvisorized user to delete an advisor', function () {
    // Cria um autor para testar a exclusão
    $createData = advisor::create(['name' => 'advisor Name']);

    // Simula um usuário que não tem permissão para excluir
    $unadvisorizedUser = User::factory()->create(); // Ou qualquer método para criar um usuário sem permissões

    // Faz a requisição para deletar o autor com o usuário não autorizado
    $response = $this->actingAs($unadvisorizedUser)->delete(route('advisor.destroy', $createData->id));

    // Verifica se a resposta é um erro de autorização (403 Forbidden)
    $response->assertStatus(403);

    // Verifica se o autor ainda existe no banco de dados
    $this->assertDatabaseHas('advisors', ['id' => $createData->id]);
});


it('can list advisors with filters and pagination', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('advisor.index'));

    $datas = [
        'advisor 1',
        'advisor 2',
        'advisor User'
    ];

    foreach ($datas as $advisor) {
        advisor::create(['name' => $advisor]);
    }

    $response = $this->get(route('advisor.index'));
    $response->assertStatus(200);

    $response->assertInertia(
        fn ($page) => $page
            ->component('Admin/Advisor/Index')
            ->has('fields')
            ->has('data')
            ->has('can')
    );

    $response->assertInertia(
        fn ($page) => $page
            ->component('Admin/Advisor/Index')
            ->where('data', function ($datas) {
                return count($datas["data"]) === 3;
            })
    );

    $response = $this->get(route('advisor.index', ['q' => 'user']));

    $response->assertInertia(
        fn ($page) => $page
            ->where('data', function ($datas) {
                return count($datas["data"]) === 1;
            })
    );
});

it('requires name to be present', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('advisor.index'));
    $response = $this->post(route('advisor.store'), []);
    $response->assertSessionHasErrors('name');
});

it('requires name to have max length', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('advisor.index'));

    $response = $this->post(route('advisor.store'), [
        'name' => str_repeat('t',300),
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('name');
});