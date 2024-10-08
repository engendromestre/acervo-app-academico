<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentVisit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    private $request = null;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        if (
            $request->q == ''
            && $request->selCollections == ''
            && $request->selCourses == ''
            && $request->selPublicationYear == ''
        ) {
            $documents = ['msg' => 'Pesquise Algo'];
            $sql = '';
        } else {
            $documents = (new Document)->newQuery();
            $documents->with('collection:id,name');
            $documents->with('course:id,name');
            $documents->with('author:id,name');
            $documents->with('advisor:id,name');
            $documents->latest();

            $documents = $documents
            ->join('authors', 'documents.author_id', '=', 'authors.id')
            ->join('advisors', 'documents.advisor_id', '=', 'advisors.id')
            ->select('documents.*', 'authors.name as author_name', 'advisors.name as advisor_name');

            $documents = $documents->when(
                $request->q,
                function ($query, $req) {
                    $this->request = $req;
                    $query->where(function ($queryInternal) {
                        $queryInternal
                            ->whereRaw("UPPER(title) LIKE ?", "%" . mb_strtoupper($this->request, 'UTF-8') . "%")
                            ->orWhereRaw("UPPER(subtitle) LIKE ?", "%" . mb_strtoupper($this->request, 'UTF-8') . "%")
                            ->orWhereRaw("UPPER(authors.name) LIKE ?", "%" . mb_strtoupper($this->request, 'UTF-8') . "%")
                            ->orWhereRaw("UPPER(advisors.name) LIKE ?", "%" . mb_strtoupper($this->request, 'UTF-8') . "%");
                    });
                    $this->request = null;
                }
            );

            $documents = $documents->when(
                $request->selCollections,
                function ($query, $req) {
                    $query->where("collection_id", "=", $req);
                }
            );

            $documents = $documents->when(
                $request->selCourses,
                function ($query, $req) {
                    $query->where("course_id", "=", $req);
                }
            );

            $documents = $documents->when(
                $request->selPublicationYear,
                function ($query, $req) {
                    $query->where("publicationYear", "=", $req);
                }
            );

            $sql =  $documents->toSql();
            $documents = $documents->get();

            if ($documents->isEmpty()) {
                $documents = ['msg' => 'Nenhum recurso Encontrado'];
            }
        }
        return Inertia::render('Welcome', [
            'data' => $documents,
            'courses' =>  $request->courses,
            'collections' =>  $request->collections,
            'canLogin' => $request->canLogin,
            'fields' => $request->fields,
            'sql' => $sql
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function visitsIncrement(Request $request)
    {
        $document = Document::find($request->id);
        $documentVisit = DocumentVisit::create([
            'document_id'=>$document->id,
            'ip_address' => implode($request->ips()),
        ]);
        return Inertia::render('Welcome', [
            'document' => $document,
            'data' => $request->documents,
            'collections' =>  $request->collections,
            'courses' =>  $request->courses,
            'canLogin' => $request->canLogin,
            'fields' => $request->fields,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getVisit(Request $request)
    {
        $visits = DocumentVisit::where('document_id','=',$request->id)->count();
        return response()->json($visits, 200);
    }
}
