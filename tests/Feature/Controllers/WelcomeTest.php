<?php
use App\Http\Controllers\WelcomeController;
use App\Models\Document;
use App\Models\DocumentVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

it('displays a message when no search criteria are provided', function () {
    $response = $this->post(route('welcome'), [
        'q' => '',
        'selCollections' => '',
        'selCourses' => '',
        'selPublicationYear' => '',
    ]);

    $response->assertInertia(
        fn ($page) => $page
            ->component('Welcome')
            ->where('data', function ($d) {
                return $d["msg"] === "Pesquise Algo";
            })
            
    );
});

it('returns documents based on search criteria', function () {
    // Cria alguns documentos para serem encontrados.
    $document = Document::factory()->create(['title' => 'Teste de Pesquisa']);

    $response = $this->post(route('welcome'), [
        'q' => 'Teste de Pesquisa',
        'selCollections' => '',
        'selCourses' => '',
        'selPublicationYear' => '',
    ]);

    $response->assertInertia(
        fn ($page) => $page
            ->component('Welcome')
            ->has('data', 1)
    );
});

it('increments document visits', function () {
    $document = Document::factory()->create();

    $response = $this->patch(route('welcome.visitsIncrement'), [
        'id' => $document->id,
    ]);

    $this->assertDatabaseHas('document_visits', [
        'document_id' => $document->id,
    ]);

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Welcome')
        ->has('document', fn (Assert $doc) => $doc
            ->where('id', $document->id)
            ->etc()
        )
    );
});

it('returns the visit count for a document', function () {
    $document = Document::factory()->create();
    
    DB::table('document_visits')->insert([
        'document_id' => $document->id,
        'ip_address' => '192.168.1.1', // Exemplo de IP
        'visited_at' => now(),
    ]);

    DB::table('document_visits')->insert([
        'document_id' => $document->id,
        'ip_address' => '192.168.1.2', // Exemplo de IP
        'visited_at' => now(),
    ]);

    $response = $this->get(route('welcome.getVisit', ['id' => $document->id]));

    $data = $response->json();
    $response->assertOk();
    $this->assertEquals('2', $data);
});
