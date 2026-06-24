<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', function () {
    $urls = [
        route('home'), route('about'), route('services'), route('events'), route('contact'),
        route('en.home'), route('en.about'), route('en.services'), route('en.events'), route('en.contact'),
    ];

    return response()
        ->view('sitemap', compact('urls'))
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/sobre-nos', [PageController::class, 'about'])->name('about');
Route::get('/servicos', [PageController::class, 'services'])->name('services');
Route::get('/eventos', [PageController::class, 'events'])->name('events');
Route::get('/contactos', [PageController::class, 'contact'])->name('contact');
Route::post('/contactos', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

Route::prefix('en')->name('en.')->group(function (): void {
    Route::get('/', [PageController::class, 'homeEn'])->name('home');
    Route::get('/about', [PageController::class, 'aboutEn'])->name('about');
    Route::get('/services', [PageController::class, 'servicesEn'])->name('services');
    Route::get('/events', [PageController::class, 'eventsEn'])->name('events');
    Route::get('/contact', [PageController::class, 'contactEn'])->name('contact');
    Route::post('/contact', [ContactController::class, 'storeEn'])
        ->middleware('throttle:5,1')
        ->name('contact.store');
});

Route::redirect('/index.html', '/', 301);
Route::redirect('/about.html', '/sobre-nos', 301);
Route::redirect('/services.html', '/servicos', 301);
Route::redirect('/event.html', '/eventos', 301);
Route::redirect('/noevent.html', '/eventos', 301);
Route::redirect('/contact.html', '/contactos', 301);
