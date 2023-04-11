<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CollectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:collection list', ['only' => ['index', 'show']]);
        $this->middleware('can:collection create', ['only' => ['create', 'store']]);
        $this->middleware('can:collection edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:collection delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $collections = (new Collection)->newQuery();
        $collections->latest();
        $collections = $collections->when(
            $request->q,function($query,$q)
            {
                $query->where('name','LIKE','%'. $q .'%');
            }
        )->paginate(8);
        $fields = (new Collection)->getFields();
        return Inertia::render('Admin/Collection/Index', [
            'fields' => $fields,
            'data' => $collections,
            'can' => [
                'create' => Auth::user()->can('collection create'),
                'edit' => Auth::user()->can('collection edit'),
                'delete' => Auth::user()->can('collection delete'),
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
        $collection = new Collection();
        $request->validate($collection->rules());
        $collection->create($request->all());
        return redirect()->route('collection.index',['page' => $request->input('page')])->with('message', 'Created Successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Collection  $collection
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Collection $collection)
    {
        $request->validate( $collection->rules() );
        $collection->update($request->all());
        return redirect()->route('collection.index',['page' => $request->input('page')])->with('message', 'Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Collection  $collection
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,Collection $collection)
    {
        $collection->delete();
        return redirect()->route('collection.index',['page' => $request->input('page')])->with('message', 'Deleted Successfully');
    }
}
