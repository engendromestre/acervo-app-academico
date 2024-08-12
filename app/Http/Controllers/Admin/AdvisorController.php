<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdvisorController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:advisor list', ['only' => ['index', 'show']]);
        $this->middleware('can:advisor create', ['only' => ['create', 'store']]);
        $this->middleware('can:advisor edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:advisor delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $advisors = (new Advisor)->newQuery();
        $advisors->latest();
        $advisors = $advisors->when(
            $request->q,function($query,$q)
            {
                $query->where('name','LIKE','%'. $q .'%');
            }
        )->paginate(8);
        $fields = (new advisor)->getFields();
        return Inertia::render('Admin/Advisor/Index', [
            'fields' => $fields,
            'data' => $advisors,
            'can' => [
                'list' => Auth::user()->can('advisor list'),
                'create' => Auth::user()->can('advisor create'),
                'edit' => Auth::user()->can('advisor edit'),
                'delete' => Auth::user()->can('advisor delete'),
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
        $advisors = new Advisor();
        $request->validate($advisors->rules());
        $advisors->create($request->all());
        return redirect()->route('advisor.index',['page' => $request->input('page')])->with('message', 'Created Successfully');
    }

     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Advisor  $advisor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Advisor $advisor)
    {
        $request->validate( $advisor->rules() );
        $advisor->update($request->all());
        return redirect()->route('advisor.index',['page' => $request->input('page')])->with('message', 'Updated Successfully');
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Advisor  $advisor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,Advisor $advisor)
    {
        $advisor->delete();
        return redirect()->route('advisor.index',['page' => $request->input('page')])->with('message', 'Deleted Successfully');
    }
}
