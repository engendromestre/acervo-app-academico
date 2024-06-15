<?php
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;

beforeEach(function () {
    $this->course = new Course();
});

test('test if use traits', function() {
    $traits = [
        HasFactory::class
    ];
    $courseTraits = array_keys(class_uses(Course::class));
    expect($traits)->toBe($courseTraits);
});

test('test fillable attribute', function () {
    $fillable = ['name'];
    expect($fillable)->toBe($this->course->getFillable());
});