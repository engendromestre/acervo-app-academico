<?php
use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\HasFactory;

beforeEach(function () {
    $this->document = new Document();
});

test('test if use traits', function() {
    $traits = [
        HasFactory::class
    ];
    $documentTraits = array_keys(class_uses(Document::class));
    expect($traits)->toBe($documentTraits);
});

test('test fillable attribute', function () {
    $fillable = ['title', 'subtitle', 'collection_id', 'course_id', 'author_id', 'advisor_id','file','publicationYear'];
    expect($fillable)->toBe($this->document->getFillable());
});

