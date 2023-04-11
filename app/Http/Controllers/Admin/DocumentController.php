<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:document list', ['only' => ['index', 'show']]);
        $this->middleware('can:document create', ['only' => ['create', 'store']]);
        $this->middleware('can:document edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:document delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $documents = (new Document)->newQuery();
        $documents->with('collection:id,name');
        $documents->with('course:id,name');
        $documents->latest();
        $documents = $documents->when(
            $request->q,
            function ($query, $req) {
                $this->request = $req;
                $query
                    ->where(function($subQuery) {
                        $subQuery
                        ->whereHas('collection', function ( $query ) {
                            $query ->whereRaw("UPPER(name) LIKE ?", "%" . mb_strtoupper($this->request, 'UTF-8') . "%");
                        })
                        ->orWhereHas('course', function ( $query ) {
                            $query ->whereRaw("UPPER(name) LIKE ?", "%" . mb_strtoupper($this->request, 'UTF-8') . "%");
                        });
                    })
                    ->orWhere(function ($subQuery) {
                        $subQuery
                            ->whereRaw("UPPER(title) LIKE ?", "%" . mb_strtoupper($this->request, 'UTF-8') . "%")
                            ->orWhereRaw("UPPER(subtitle) LIKE ?", "%" . mb_strtoupper($this->request, 'UTF-8') . "%")
                            ->orWhereRaw("UPPER(author) LIKE ?", "%" . mb_strtoupper($this->request, 'UTF-8') . "%")
                            ->orWhereRaw("UPPER(advisor) LIKE ?", "%" . mb_strtoupper($this->request, 'UTF-8') . "%");
                    });
                $this->request = null;
            }
        );

        $documents = $documents->paginate(8);
        $fields = (new Document)->getFields();
        $fields['collection_id']['fixedValues'] = DB::table('collections')->select('id', 'name')->get();
        $fields['course_id']['fixedValues'] = DB::table('courses')->select('id', 'name')->get();
        return Inertia::render('Admin/Document/Index', [
            'fields' => $fields,
            'data' => $documents,
            'can' => [
                'create' => Auth::user()->can('document create'),
                'edit' => Auth::user()->can('document edit'),
                'delete' => Auth::user()->can('document delete'),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Document $document)
    {
        $request->validate($document->rules());

        $file = $request->file('file');
        $fileUrn = $file->store('documents', 'public');

        $document->create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'collection_id' => $request->collection_id,
            'course_id' => $request->course_id,
            'author' => $request->author,
            'advisor' =>   $request->advisor,
            'file' => $fileUrn,
            'publicationYear' => $request->publicationYear
        ]);
        return redirect()->route('document.index', ['page' => $request->input('page')])->with('message', 'Created Successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Document $document)
    {
        $rules = $document->rules();
        $rules['file'] = ["required"];
        $request->validate($rules);
        $document->update($request->all());
        return redirect()->route('document.index', ['page' => $request->input('page')])->with('message', 'Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Document $document)
    {
        if ($document === null) {
            return response()->json(
                ['error' => 'Unable to perform deletion. The requested resource does not exist'],
                404
            );
        }
        Storage::disk('public')->delete($document->file);
        $document->delete();
    }
}
