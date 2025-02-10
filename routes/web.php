<?php


use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\CertificationController;
use App\Http\Controllers\DisciplineController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\AccessController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StudentTaskController;
use App\Http\Controllers\StudentTestController;
use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;


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
    return view('site.welcome');
});

Auth::routes();

Route::middleware(['auth', 'institution.scope'])->group(function () {

    // Access
    Route::resource('access', AccessController::class)->only(['index', 'destroy']);

    // ADMIN
    Route::middleware('can:access-admin')->group(function () {

        // Teachers
        Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
        Route::get('/teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
        Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
        Route::get('/teachers/{id}', [TeacherController::class, 'show'])->name('teachers.show');
        Route::get('/teachers/{id}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
        Route::put('/teachers/{id}', [TeacherController::class, 'update'])->name('teachers.update');
        Route::delete('/teachers/{id}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
        // Students
        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');
        Route::get('/students/{id}', [StudentController::class, 'show'])->name('students.show');
        Route::get('/students/{id}/edit', [StudentController::class, 'edit'])->name('studentss.edit');
        Route::put('/students/{id}', [StudentController::class, 'update'])->name('students.update');
        Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('students.destroy');
        // Modules
        Route::get('/modules', [ModuleController::class, 'index'])->name('modules.index');
        Route::get('/modules/create', [ModuleController::class, 'create'])->name('modules.create');
        Route::post('/modules', [ModuleController::class, 'store'])->name('modules.store');
        Route::get('/modules/{id}', [ModuleController::class, 'show'])->name('modules.show');
        Route::get('/modules/{id}/edit', [ModuleController::class, 'edit'])->name('modules.edit');
        Route::put('/modules/{id}', [ModuleController::class, 'update'])->name('modules.update');
        Route::delete('/modules/{id}', [ModuleController::class, 'destroy'])->name('modules.destroy');
        //Certifications
        Route::get('/certifications', [CertificationController::class, 'index'])->name('certifications.index');
        Route::get('/certifications/create', [CertificationController::class, 'create'])->name('certifications.create');
        Route::post('/certifications', [CertificationController::class, 'store'])->name('certifications.store');
        Route::get('/certifications/{id}', [CertificationController::class, 'show'])->name('certifications.show');
        Route::get('/certifications/{id}/edit', [CertificationController::class, 'edit'])->name('certifications.edit');
        Route::put('/certifications/{id}', [CertificationController::class, 'update'])->name('certifications.update');
        Route::delete('/certifications/{id}', [CertificationController::class, 'destroy'])->name('certifications.destroy');
        // Disciplines
        Route::get('/disciplines', [DisciplineController::class, 'index'])->name('disciplines.index');
        Route::get('/disciplines/create', [DisciplineController::class, 'create'])->name('disciplines.create');
        Route::post('/disciplines', [DisciplineController::class, 'store'])->name('disciplines.store');
        Route::get('/disciplines/{id}', [DisciplineController::class, 'show'])->name('disciplines.show');
        Route::get('/disciplines/{id}/edit', [DisciplineController::class, 'edit'])->name('disciplines.edit');
        Route::put('/disciplines/{id}', [DisciplineController::class, 'update'])->name('disciplines.update');
        Route::delete('/disciplines/{id}', [DisciplineController::class, 'destroy'])->name('disciplines.destroy');
        Route::get('/disciplines/{id}/resources', [DisciplineController::class, 'resources'])->name('disciplines.resources');
        Route::post('/disciplines/{id}/add-resource', [DisciplineController::class, 'addResource'])->name('disciplines.addResource');

        // Resources
        Route::get('/resources/{id}/edit', [ResourceController::class, 'edit'])->name('resources.edit');
        Route::put('/resources/{id}', [ResourceController::class, 'update'])->name('resources.update');
        Route::delete('/resources/{id}', [ResourceController::class, 'destroy'])->name('resources.destroy');

        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    });
    // Student access
    Route::get('/listdisciplines', [DisciplineController::class, 'listDisciplines'])->name('disciplines.listDisciplines');
    Route::get('/list-certifications', [CertificationController::class, 'listCertifications'])->name('certifications.listCertifications');
    Route::get('/mycertifications', [CertificationController::class, 'myCertifications'])->name('certifications.myCertifications');
    Route::get('/mydisciplines', [DisciplineController::class, 'myDisciplines'])->name('disciplines.myDisciplines');
    Route::get('/resources/{id}/docs', [ResourceController::class, 'docs'])->name('resources.docs');
    Route::get('/resources/{id}/tasks', [ResourceController::class, 'tasks'])->name('resources.tasks');
    Route::get('/resources/{id}/test', [ResourceController::class, 'tests'])->name('resources.tests');
    Route::get('/task/{id}/edit', [StudentTaskController::class, 'edit'])->name('edit');
    Route::post('/studentTasks', [StudentTaskController::class, 'addTask'])->name('addTask');
    Route::get('/test/{id}/edit', [StudentTestController::class, 'edit'])->name('edit');
    Route::post('/tests/submit', [StudentTestController::class, 'submitTest'])->name('submitTest');

    // Paypall
    Route::get('paypal/payment/{id}', [PayPalController::class, 'createPayment'])->name('paypal.payment');
    Route::get('paypal/capture', [PayPalController::class, 'capturePayment'])->name('paypal.capture');
    Route::get('payment/success', function () {
        return view('paypal.payment-success');
    })->name('success');
    Route::get('payment/error', function () {
        return view('paypal.payment-failed');
    })->name('error');
    Route::get('test/paypal', function () {
        return view('paypal.test-paypal');
    })->name('test.paypal');

});

Route::middleware('auth')->group(function () {
    Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);
    Route::post('/formsubmit', [App\Http\Controllers\HomeController::class, 'FormSubmit'])->name('FormSubmit');
    Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index']);
});



//Route::get('/', [App\Http\Controllers\HomeController::class, 'root']);

// Authenticated routes

