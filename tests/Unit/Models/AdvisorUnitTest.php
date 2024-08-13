<?php
use App\Models\Advisor;
use Illuminate\Database\Eloquent\Factories\HasFactory;

beforeEach(function () {
    $this->advisor = new Advisor();
});

test('test if use traits', function() {
    $traits = [
        HasFactory::class
    ];
    $advisorTraits = array_keys(class_uses(Advisor::class));
    expect($traits)->toBe($advisorTraits);
});

test('test fillable attribute', function () {
    $fillable = ['name'];
    expect($fillable)->toBe($this->advisor->getFillable());
});
