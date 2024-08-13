<?php
use App\Http\Controllers\WelcomeController;
use App\Models\Document;
use App\Models\DocumentVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

// it('displays a message when no search criteria are provided', function () {
//     $response = $this->post(route('welcome'), [
//         'q' => '',
//         'selCollections' => '',
//         'selCourses' => '',
//         'selPublicationYear' => '',
//     ]);

//     $response->assertInertia(
//         fn ($page) => $page
//             ->component('Welcome')
//             ->where('data', function ($d) {
//                 return $d["msg"] === "Pesquise Algo";
//             })
            
//     );
// });

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
    );


});
