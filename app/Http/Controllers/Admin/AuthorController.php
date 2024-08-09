<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthorController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:author list', ['only' => ['index', 'show']]);
        $this->middleware('can:author create', ['only' => ['create', 'store']]);
        $this->middleware('can:author edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:author delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $authors = (new Author)->newQuery();
        $authors->latest();
        $authors = $authors->when(
            $request->q,function($query,$q)
            {
                $query->where('name','LIKE','%'. $q .'%');
            }
        )->paginate(8);
        $fields = (new Author)->getFields();
        return Inertia::render('Admin/Author/Index', [
            'fields' => $fields,
            'data' => $authors,
            'can' => [
                'list' => Auth::user()->can('author list'),
                'create' => Auth::user()->can('author create'),
                'edit' => Auth::user()->can('author edit'),
                'delete' => Auth::user()->can('author delete'),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $authors = new Author();
        $request->validate($authors->rules());
        $authors->create($request->all());
        return redirect()->route('author.index',['page' => $request->input('page')])->with('message', 'Created Successfully');
    }

     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Author $author)
    {
        $request->validate( $author->rules() );
        $author->update($request->all());
        return redirect()->route('author.index',['page' => $request->input('page')])->with('message', 'Updated Successfully');
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,Author $author)
    {
        $author->delete();
        return redirect()->route('author.index',['page' => $request->input('page')])->with('message', 'Deleted Successfully');
    }
}
