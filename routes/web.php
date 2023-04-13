<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Collection;
use App\Models\Course;
use App\Models\Document;
use App\Http\Controllers\WelcomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $documents = new Document;
    $fieldVisitDocument = $documents->getFieldsWelcome();
    $collections = Collection::select('id','name')->get();
    $courses = Course::select('id','name')->get();
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'courses' =>  $courses,
        'collections' =>  $collections,
        'fields' => $fieldVisitDocument
    ]);
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::group([
    'namespace' => 'App\Http\Controllers\Admin',
    'prefix' => 'admin',
    'middleware' => ['auth'],
], function () {
    Route::resource('user', 'UserController');
    Route::resource('role', 'RoleController');
    Route::resource('permission', 'PermissionController');
    Route::resource('collection', 'CollectionController');
    Route::resource('course', 'CourseController');
    Route::resource('document', 'DocumentController');
});

Route::post('/{id}',[WelcomeController::class,'getVisit'])->name('welcome.getVisit');
Route::post('/',[WelcomeController::class,'list'])->name('welcome');
Route::patch('/',[WelcomeController::class,'visitsIncrement'])->name('welcome.visitsIncrement');
