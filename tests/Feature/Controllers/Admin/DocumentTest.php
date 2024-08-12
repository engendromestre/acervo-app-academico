<?php

use Faker\Generator as Faker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Models\User;
use App\Models\Collection;
use App\Models\Course;
use App\Models\Document;
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

it('can create document', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('document.index'));

    Storage::fake('s3');
    $faker = app(Faker::class);
    $file = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf');

    $title = $faker->word;
    $data = [
        'title' => $title,
        'subtitle' => $faker->word,
        'collection_id' => Collection::factory()->create()->id,
        'course_id' => Course::factory()->create()->id,
        'author_id' => $faker->name,
        'advisor' => $faker->name,
        'file' => $file,
        'publicationYear' => $faker->year
    ];

    $response = $this->post(route('document.store'), $data);
    $response->assertRedirect(route('document.index'));
    $this->assertDatabaseHas('documents', ['title' =>  $title]);
    /** @phpstan-ignore-next-line */
    //Storage::disk('public')->assertExists("documents/{$file->hashName()}");
    $this->assertTrue(Storage::disk('s3')->exists("documents/{$file->hashName()}"));
});

it('can update document', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('document.index'));

    $faker = app(Faker::class);

    // Crie uma permissão para testar a atualização
    $document = Document::factory()->create();

    $updateData = [
        'title' => 'Updated Document Title',
        'subtitle' => $faker->word,
        'collection_id' => Collection::factory()->create()->id,
        'course_id' => Course::factory()->create()->id,
        'author_id' => 'Updated Author Name',
        'file' => 'document.pdf',
        'advisor' => $faker->name,
        'publicationYear' => $faker->year,
    ];

    $response = $this->put(route('document.update', $document), $updateData);
    //dd($response);
    $response->assertRedirect(route('document.index'));
    $this->assertDatabaseHas('documents', [
        'id' => $document->id,
        'title' => 'Updated Document Title',
        'author_id' => 'Updated Author Name',
    ]);
});

it('can delete a document', function () {
    Storage::fake('s3');
    $this->actingAs($this->superAdmin)->get(route('document.index'));

    $document = Document::factory()->create();

    $this->delete(route('document.destroy', $document));

    $this->assertDatabaseMissing('documents', [
        'id' => $document->id,
    ]);
    // Verificar se o arquivo foi removido do disco fake S3
    Storage::disk('s3')->assertMissing("documents/{$document->file}");
});

it('can list documents with filters and pagination', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('document.index'));

    Document::factory()->count(10)->create();
    Document::factory()->create(['title' => 'Test Document']);
    Document::factory()->create(['title' => 'Test Other Document']);

    $response = $this->get(route('document.index'));
    $response->assertStatus(200);

    $response->assertInertia(
        fn ($page) => $page
            ->component('Admin/Document/Index')
            ->has('fields')
            ->has('data')
            ->has('can')
    );

    $response->assertInertia(
        fn ($page) => $page
            ->component('Admin/Document/Index')
            ->where('data', function ($datas) {
                return count($datas["data"]) === 8;
            })
    );

    $response = $this->get(route('document.index', ['q' => 'test']));

    $response->assertInertia(
        fn ($page) => $page
            ->where('data', function ($datas) {
                return count($datas["data"]) === 2;
            })
    );

    
});

it('requires multiple fields to be present', function () {
    $payload = Document::factory()->make([
        'title' => '',
        'collection_id' => null,
        'course_id' => null,
        'author_id' => '',
        'advisor' => '',
        'file' => '',
        'publicationYear' => '',
    ])->toArray();

    $response = $this->actingAs($this->superAdmin)->post(route('document.store'), $payload);

    // Verifique se a resposta contém erros de validação para os campos especificados
    $response->assertSessionHasErrors([
        'title',
        'collection_id',
        'course_id',
        'author_id',
        'advisor',
        'file',
        'publicationYear',
    ]);
    
    // Verifique se a resposta é um redirecionamento, indicando que a validação falhou
    $response->assertStatus(302);
});

it('requires multiples fields to have max length', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('document.index'));

    $faker = app(Faker::class);
    $file = UploadedFile::fake()->create('document.pdf', 1000, 'application/pdf');

    $data = [
        'title' => $faker->word,
        'subtitle' => $faker->word,
        'collection_id' => Collection::factory()->create()->id,
        'course_id' => Course::factory()->create()->id,
        'author_id' => $faker->name,
        'advisor' => $faker->name,
        'file' => $file,
        'publicationYear' => $faker->year
    ];

    // Test Title Field
    $data['title'] = str_repeat('t',151);

    $response = $this->post(route('document.store'), $data);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('title');

    // Test Subtitle field
    $data['title'] = $faker->word;
    $data['subtitle'] = str_repeat('t',151);

    $response = $this->post(route('document.store'), $data);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('subtitle');

    $data['subtitle'] = $faker->word;

    $response = $this->post(route('document.store'), $data);

    $response->assertStatus(302);

     // Test Advisor field
     $data['advisor'] = str_repeat('t',151);
 
     $response = $this->post(route('document.store'), $data);
 
     $response->assertStatus(302);
     $response->assertSessionHasErrors('advisor');

     // Test File field
     $data['advisor'] =  $faker->name;
     $data['file'] = str_repeat('t',20001) .'.pdf';
 
     $response = $this->post(route('document.store'), $data);
 
     $response->assertStatus(302);
     $response->assertSessionHasErrors('file');
});
