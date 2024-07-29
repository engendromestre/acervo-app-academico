<?php

use App\Http\Controllers\Auth\ProviderController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Collection;
use App\Models\Course;
use App\Models\Document;
use App\Http\Controllers\WelcomeController;
use App\Models\User;

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

Route::get(
    '/auth/{provider}/redirect',
    [ProviderController::class, 'redirect']
)
    ->name('social.redirect');

Route::get(
    '/auth/{provider}/callback',
    [ProviderController::class, 'callback']
)->name('social.callback');

Route::get('/', function () {
    $documents = new Document;
    $fieldVisitDocument = $documents->getFieldsWelcome();
    $collections = Collection::select('id', 'name')->get();
    $courses = Course::select('id', 'name')->get();
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

require __DIR__ . '/auth.php';

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

Route::post('/{id}', [WelcomeController::class, 'getVisit'])->name('welcome.getVisit');
Route::post('/', [WelcomeController::class, 'list'])->name('welcome');
Route::patch('/', [WelcomeController::class, 'visitsIncrement'])->name('welcome.visitsIncrement');


// Route::get('/sendmailtest', function() {
//     $user = User::where('email', env('APP_SUPERADMIN'))->first();
//     return new \App\Mail\NotifySocialiteUser($user);
// });

// Route::get('/uploadtest', function() {
//     try {
//         $upload = Storage::put('example2.jpg',file_get_contents('https://img.freepik.com/psd-gratuitas/3d-flor-rosa-isolada-em-fundo-transparente_191095-16624.jpg'),'public');
//         dd($upload);
//     } catch (\Exception $e) {
//         return $e->getMessage();
//     }
// });