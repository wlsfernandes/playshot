<?php


use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\PaymentController;
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
Route::get('/lang/{lang}', function ($lang) {
    Session::put('locale', $lang);
    return redirect()->back();
})->name('lang.switch');

Route::get('/about-us', [HomeController::class, 'aboutUs'])->name('about_us');
Route::get('/our-team', [HomeController::class, 'ourTeam'])->name('our_team');
Route::get('/open-positions', [HomeController::class, 'openPositions'])->name('open_positions');

Route::get('/blog', [HomeController::class, 'blog'])->name('blog');
Route::get('/contact', [HomeController::class, 'contactUs'])->name('contact_us');
Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/certification-program', [HomeController::class, 'certificationProgram'])->name('certification_program');
Route::get('/request-certification', [HomeController::class, 'requestCertification'])->name('requestCertification');
Route::get('/certified-institutions', [HomeController::class, 'certifiedInstitutions'])->name('certified_institutions');
Route::get('/memberships', [HomeController::class, 'memberships'])->name('memberships');
Route::get('/antioquia', [HomeController::class, 'antioquia'])->name('antioquia');
Route::get('/resource-center', [HomeController::class, 'resourceCenter'])->name('resource_center');
Route::get('/young-leaders', [HomeController::class, 'youngLeaders'])->name('young-leaders');
Route::get('/compelling-preaching', [HomeController::class, 'compellingPreaching'])->name('compelling_preaching');

Route::get('/donations', [HomeController::class, 'donations'])->name('donations');
Route::get('/aeth-fund', [HomeController::class, 'aethFund'])->name('aeth_fund');
Route::get('/gonzalez-center', [HomeController::class, 'gonzalezCenter'])->name('gonzalez_center');
Route::post('/members', [HomeController::class, 'store'])->name('members.store');


Route::get('/web-application', [HomeController::class, 'webApplication'])->name('webApplication');



Route::get('/testimonials', [HomeController::class, 'testimonials'])->name('testimonials');



Auth::routes();

Route::middleware(['auth', 'institution.scope'])->group(function () {

    // ADMIN
    Route::middleware('can:access-admin')->group(function () {

        // Teachers
        /*
        Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
        Route::get('/teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
        Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
        Route::get('/teachers/{id}', [TeacherController::class, 'show'])->name('teachers.show');
        Route::get('/teachers/{id}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
        Route::put('/teachers/{id}', [TeacherController::class, 'update'])->name('teachers.update');
        Route::delete('/teachers/{id}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
        */
        // Students


        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    });
    // Student access

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

