<?php

use App\Models\User;
use App\Models\Author;
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

it('can create author', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('author.index'));

    $data = [
        'name' => 'User Author',
    ];

    $response = $this->post(route('author.store'), $data);

    $response->assertRedirect(route('author.index'));
    $this->assertDatabaseHas('authors', ['name' => 'User Author']);
});

it('can create author with bio', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('author.index'));

    $data = [
        'name' => 'User Author',
        'bio' => 'Bio Author',
    ];

    $response = $this->post(route('author.store'), $data);

    $response->assertRedirect(route('author.index'));
    $this->assertDatabaseHas('authors', ['name' => 'User Author']);
});

it('does not allow author creation with bio longer than 255 characters', function () {
    // Cria uma biografia com mais de 255 caracteres
    $longBio = str_repeat('a', 256); 

    $data = [
        'name' => 'User Author',
        'bio' => $longBio,
    ];

    // Faz a requisição com a biografia longa
    $response = $this->actingAs($this->superAdmin)->post(route('author.store'), $data);

    // Verifica que a resposta é uma redireção, indicando que o processamento falhou
    $response->assertSessionHasErrors('bio');

    // Opcional: Verifique se o autor não foi salvo no banco de dados
    $this->assertDatabaseMissing('authors', ['name' => 'User Author', 'bio' => $longBio]);
});

it('can update author', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('author.index'));

    // Crie um registro para testar a atualização
    $createData = Author::create(['name' => 'Author Name']);

    $data = Author::where('id',  $createData->id)->first();

    // Dados de atualização
    $updateData = [
        'name' => 'New Author Name',
    ];

    $response = $this->put(route('author.update', $data), $updateData);
    $response->assertRedirect(route('author.index'));
    $this->assertDatabaseHas('authors', ['id' => $data->id, 'name' => 'New Author Name']);
});

it('does not allow updating author with name longer than 255 characters', function () {
    // Crie um registro para testar a atualização
    $createData = Author::create(['name' => 'Author Name']);

    // Crie um nome com mais de 255 caracteres
    $longName = str_repeat('a', 256);

    // Dados de atualização
    $updateData = [
        'name' => $longName,
    ];

    // Tenta atualizar o autor com um nome longo
    $response = $this->actingAs($this->superAdmin)->put(route('author.update', $createData->id), $updateData);

    // Verifica que a resposta contém erros de validação para o campo 'name'
    $response->assertSessionHasErrors('name');

    // Opcional: Verifique se o nome não foi alterado no banco de dados
    $this->assertDatabaseHas('authors', ['id' => $createData->id, 'name' => 'Author Name']);
});

it('can delete a author', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('author.index'));

    $createData = Author::create(['name' => 'Author Name']);

    $response = $this->delete(route('author.destroy', $createData));

    $response->assertRedirect(route('author.index'));
    $this->assertDatabaseMissing('authors', [
        'id' => $createData->id,
    ]);
});

it('does not allow unauthorized user to delete an author', function () {
    // Cria um autor para testar a exclusão
    $createData = Author::create(['name' => 'Author Name']);

    // Simula um usuário que não tem permissão para excluir
    $unauthorizedUser = User::factory()->create(); // Ou qualquer método para criar um usuário sem permissões

    // Faz a requisição para deletar o autor com o usuário não autorizado
    $response = $this->actingAs($unauthorizedUser)->delete(route('author.destroy', $createData->id));

    // Verifica se a resposta é um erro de autorização (403 Forbidden)
    $response->assertStatus(403);

    // Verifica se o autor ainda existe no banco de dados
    $this->assertDatabaseHas('authors', ['id' => $createData->id]);
});


it('can list authors with filters and pagination', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('author.index'));

    $datas = [
        'Author 1',
        'Author 2',
        'Author User'
    ];

    foreach ($datas as $author) {
        Author::create(['name' => $author]);
    }

    $response = $this->get(route('author.index'));
    $response->assertStatus(200);

    $response->assertInertia(
        fn ($page) => $page
            ->component('Admin/Author/Index')
            ->has('fields')
            ->has('data')
            ->has('can')
    );

    $response->assertInertia(
        fn ($page) => $page
            ->component('Admin/Author/Index')
            ->where('data', function ($datas) {
                return count($datas["data"]) === 3;
            })
    );

    $response = $this->get(route('author.index', ['q' => 'user']));

    $response->assertInertia(
        fn ($page) => $page
            ->where('data', function ($datas) {
                return count($datas["data"]) === 1;
            })
    );
});

it('requires name to be present', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('author.index'));
    $response = $this->post(route('author.store'), []);
    $response->assertSessionHasErrors('name');
});

it('requires name to have max length', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('author.index'));

    $response = $this->post(route('author.store'), [
        'name' => str_repeat('t',300),
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('name');
});

