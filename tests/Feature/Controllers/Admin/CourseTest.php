<?php

use App\Models\User;
use App\Models\Course;
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

it('can create course', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('course.index'));

    $data = [
        'name' => 'Administration'
    ];

    $response = $this->post(route('course.store'), $data);

    $response->assertRedirect(route('course.index'));
    $this->assertDatabaseHas('courses', ['name' => 'Administration']);
});

it('can update course', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('course.index'));

    // Crie uma permissão para testar a atualização
    $createData = Course::create(['name' => 'Administration']);

    $data = Course::where('id',  $createData->id)->first();

    // Dados de atualização
    $updateData = [
        'name' => 'Nursing',
    ];

    $response = $this->put(route('course.update', $data), $updateData);
    $response->assertRedirect(route('course.index'));
    $this->assertDatabaseHas('courses', ['id' => $data->id, 'name' => 'Nursing']);
});

it('can delete a course', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('course.index'));

    $createData = Course::create(['name' => 'Administration']);

    $response = $this->delete(route('course.destroy', $createData));

    $response->assertRedirect(route('course.index'));
    $this->assertDatabaseMissing('courses', [
        'id' => $createData->id,
    ]);
});

it('can list courses with filters and pagination', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('course.index'));

    $datas = [
        'Administration',
        'Nursing',
        'Computer Engineering',
        'Civil Engineering'
    ];

    foreach ($datas as $course) {
        Course::create(['name' => $course]);
    }

    $response = $this->get(route('course.index'));
    $response->assertStatus(200);

    $response->assertInertia(
        fn ($page) => $page
            ->component('Admin/Course/Index')
            ->has('fields')
            ->has('data')
            ->has('can')
    );

    $response->assertInertia(
        fn ($page) => $page
            ->component('Admin/Course/Index')
            ->where('data', function ($datas) {
                return count($datas["data"]) === 4;
            })
    );

    $response = $this->get(route('course.index', ['q' => 'Engineering']));

    $response->assertInertia(
        fn ($page) => $page
            ->where('data', function ($datas) {
                return count($datas["data"]) === 2;
            })
    );
});

it('requires name to be present', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('course.index'));
    $response = $this->post(route('course.store'), []);
    $response->assertSessionHasErrors('name');
});

it('requires name to be unique', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('course.index'));

    $data = [
        'Computer Engineering'
    ];
    $response = $this->post(route('course.store'), $data);

    $response = $this->post(route('course.store'), [
        'Computer Engineering'
    ]);

    $response->assertSessionHasErrors('name');
});

it('requires name to have max length', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('course.index'));

    $response = $this->post(route('course.store'), [
        'name' => str_repeat('t',151),
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('name');
});