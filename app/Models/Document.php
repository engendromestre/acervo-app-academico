<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'subtitle', 'collection_id', 'course_id', 'author', 'advisor','file','publicationYear'];

    /**
     * Return Fields scrutcture for Forms and List Components
     */
    public function getFields()
    {
        return [
            'title' => ['title' => 'Title', 'dataType' => 'text', 'value' => '', 'create' => true, 'read' => true, 'update' => true, 'position' => '0'],
            'subtitle' => ['title' => 'Subtitle', 'dataType' => 'text', 'value' => '', 'create' => true, 'read' => true, 'update' => true, 'position' => '1'],
            'collection_id' => ['listTable' =>'collection','listField' => 'name','fixedValues' => [],'title' => 'Collection', 'dataType' => 'select', 'value' => '', 'create' => true, 'read' => true, 'update' => true, 'position' => '2'],
            'course_id' => ['listTable' =>'course', 'listField' => 'name','fixedValues' => [],'title' => 'Course', 'dataType' => 'select', 'value' => '', 'create' => true, 'read' => true, 'update' => true, 'position' => '3'],
            'author' => ['title' => 'Author', 'dataType' => 'text', 'value' => '', 'create' => true, 'read' => true, 'update' => true, 'position' => '4'],
            'advisor' => ['title' => 'Advisor', 'dataType' => 'text', 'value' => '', 'create' => true, 'read' => true, 'update' => true, 'position' => '5'],
            'file' => ['title' => 'File', 'dataType' => 'file', 'value' => '', 'create' => true, 'read' => true, 'update' => true, 'position' => '6',
            'accept' => [ "PDF" => ['maxSizeMb' => 50,'type' => 'application/pdf' ],"PNG" => ['maxSizeMb' => 150,'type' => 'image/png' ] ] ],
            'publicationYear' => ['title' => 'Publication Year', 'dataType' => 'number', 'value' => '', 'create' => true, 'read' => true, 'update' => true, 'position' => '7'],
            'visits' => ['title' => 'Visits', 'dataType' => 'number','create' => false, 'read' => true, 'update' => false, 'position' => '8']
        ];
    }

    /**
     * Return Fields scrutcture for Forms and List Components
     */
    public function getFieldsWelcome()
    {
        return [
            'visits' => ['title' => 'Number of Visits', 'dataType' => 'number','read' => true]
        ];
    }

    /**
     * Defines the feedbacks of the validation rules
     */
    public function rules()
    {
        $yearEnd = date('Y');
        return [
            'title' => ["required", "max:150"],
            'subtitle' => ["max:150"],
            'collection_id' => ["required"],
            'course_id' => ["required"],
            'author' => ["required", "max:255"],
            'advisor' => ["required", "max:150"],
            'file' => ["required", "mimes:pdf", "max:20000"],
            'publicationYear' => ["required","max:{$yearEnd}"]
        ];
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
