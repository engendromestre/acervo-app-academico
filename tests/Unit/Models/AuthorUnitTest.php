<?php
use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\HasFactory;

beforeEach(function () {
    $this->author = new Author();
});

test('test if use traits', function() {
    $traits = [
        HasFactory::class
    ];
    $authorTraits = array_keys(class_uses(Author::class));
    expect($traits)->toBe($authorTraits);
});

test('test fillable attribute', function () {
    $fillable = ['name'];
    expect($fillable)->toBe($this->author->getFillable());
});