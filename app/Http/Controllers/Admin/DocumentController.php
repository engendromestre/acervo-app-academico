<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $documents->with('author:id,name');
        $documents->with('advisor:id,name');
        $documents->withCount('documentVisits');
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
                        })
                        ->orWhereHas('author', function ( $query ) {
                            $query ->whereRaw("UPPER(name) LIKE ?", "%" . mb_strtoupper($this->request, 'UTF-8') . "%");
                        })
                        ->orWhereHas('advisor', function ( $query ) {
                            $query ->whereRaw("UPPER(name) LIKE ?", "%" . mb_strtoupper($this->request, 'UTF-8') . "%");
                        });
                    })
                    ->orWhere(function ($subQuery) {
                        $subQuery
                            ->whereRaw("UPPER(title) LIKE ?", "%" . mb_strtoupper($this->request, 'UTF-8') . "%")
                            ->orWhereRaw("UPPER(subtitle) LIKE ?", "%" . mb_strtoupper($this->request, 'UTF-8') . "%");
                    });
                $this->request = null;
            }
        );

        $documents = $documents->paginate(8);
        $fields = (new Document)->getFields();
        $fields['collection_id']['fixedValues'] = DB::table('collections')->select('id', 'name')->orderBy('name', 'asc')->get();
        $fields['course_id']['fixedValues'] = DB::table('courses')->select('id', 'name')->orderBy('name', 'asc')->get();
        $fields['author_id']['fixedValues'] = DB::table('authors')->select('id', 'name')->orderBy('name', 'asc')->get();
        $fields['advisor_id']['fixedValues'] = DB::table('advisors')->select('id', 'name')->orderBy('name', 'asc')->get();
        
        return Inertia::render('Admin/Document/Index', [
            'fields' => $fields,
            'data' => $documents,
            'can' => [
                'list' => Auth::user()->can('document list'),
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

         // Iniciar uma transação
        DB::beginTransaction();

        try {
            // Processar o arquivo
            $file = $request->file('file');

            // Gerar um nome aleatório para o arquivo usando o método `store()`
            $randomFileName = $file->hashName();
            $filePath = 'documents/' . $randomFileName;

            // Armazenar o arquivo no S3
            Storage::disk('s3')->put($filePath, fopen($file, 'r'), 'public');

             // Obter a URL pública do arquivo
            $fileUrl = Storage::disk('s3')->url($filePath);

            $document->create([
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'collection_id' => $request->collection_id,
                'course_id' => $request->course_id,
                'author_id' => $request->author_id,
                'advisor_id' =>   $request->advisor_id,
                'file' => $fileUrl,
                'publicationYear' => $request->publicationYear
            ]);

            // Confirmar a transação
            DB::commit();
            
            return redirect()->route('document.index', ['page' => $request->input('page')])->with('message', 'Created Successfully');
        } catch (\Exception $e) {
            // Reverter a transação se algo falhar
            DB::rollBack();
            // Log de erro para depuração
            Log::error('Erro ao criar o documento: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Ocorreu um erro ao criar o documento. Por favor, tente novamente.']);
        }
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
        Storage::disk('s3')->delete($document->file);
        $document->delete();
    }
}
