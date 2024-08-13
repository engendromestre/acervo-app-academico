<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'subtitle', 'collection_id', 'course_id', 'author_id', 'advisor_id','file','publicationYear'];

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
            'author_id' => ['listTable' =>'author', 'listField' => 'name','fixedValues' => [],'title' => 'Author', 'dataType' => 'select', 'value' => '', 'create' => true, 'read' => true, 'update' => true, 'position' => '4'],
            'advisor_id' => ['listTable' =>'advisor', 'listField' => 'name','fixedValues' => [],'title' => 'Advisor', 'dataType' => 'select', 'value' => '', 'create' => true, 'read' => true, 'update' => true, 'position' => '5'],
            'file' => ['title' => 'File', 'dataType' => 'file', 'value' => '', 'create' => true, 'read' => true, 'update' => true, 'position' => '6',
            'accept' => [ "PDF" => ['maxSizeMb' => 50,'type' => 'application/pdf' ] ] ],
            'publicationYear' => ['title' => 'Publication Year', 'dataType' => 'number', 'value' => '', 'create' => true, 'read' => true, 'update' => true, 'position' => '7'],
            'document_visits_count' => ['title' => 'Visits', 'dataType' => 'number','create' => false, 'read' => true, 'update' => false, 'position' => '8']
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
            'author_id' => ["required"],
            'advisor_id' => ["required"],
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

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function advisor()
    {
        return $this->belongsTo(Advisor::class);
    }

    public function documentVisits()
    {
        return $this->hasMany(DocumentVisit::class);
    }
}
