<?php
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Factories\HasFactory;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = new Role();
});

test('test fillable attribute', function () {
    $fillable = ['name', 'guard_name', 'updated_at','created_at'];
    expect($fillable)->toBe($this->user->getFillable());
});



