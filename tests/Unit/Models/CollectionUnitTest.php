<?php
use App\Models\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;

beforeEach(function () {
    $this->collection = new Collection();
});

test('test if use traits', function() {
    $traits = [
        HasFactory::class
    ];
    $collectionTraits = array_keys(class_uses(Collection::class));
    expect($traits)->toBe($collectionTraits);
});

test('test fillable attribute', function () {
    $fillable = ['name'];
    expect($fillable)->toBe($this->collection->getFillable());
});
