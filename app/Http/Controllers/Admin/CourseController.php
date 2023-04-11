<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:course list', ['only' => ['index', 'show']]);
        $this->middleware('can:course create', ['only' => ['create', 'store']]);
        $this->middleware('can:course edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:course delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $courses = (new Course)->newQuery();
        $courses->latest();
        $courses = $courses->when(
            $request->q,function($query,$q)
            {
                $query->where('name','LIKE','%'. $q .'%');
            }
        )->paginate(8);
        $fields = (new Course)->getFields();
        return Inertia::render('Admin/Course/Index', [
            'fields' => $fields,
            'data' => $courses,
            'can' => [
                'create' => Auth::user()->can('course create'),
                'edit' => Auth::user()->can('course edit'),
                'delete' => Auth::user()->can('course delete'),
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
        $course = new Course();
        $request->validate($course->rules());
        $course->create($request->all());
        return redirect()->route('course.index',['page' => $request->input('page')])->with('message', 'Created Successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Collection  $course
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Course $course)
    {
        $request->validate( $course->rules() );
        $course->update($request->all());
        return redirect()->route('course.index',['page' => $request->input('page')])->with('message', 'Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Collection  $course
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,Course $course)
    {
        $course->delete();
        return redirect()->route('course.index',['page' => $request->input('page')])->with('message', 'Deleted Successfully');
    }
}
