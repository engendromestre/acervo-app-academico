<?php
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->permission = new Permission();
});

test('test fillable attribute', function () {
    $fillable =  [
        'name',
        'guard_name',
        'updated_at',
        'created_at',
    ];
    expect($fillable)->toBe($this->permission->getFillable());
});