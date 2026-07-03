<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\AnnouncementAdminController;
use App\Http\Controllers\AnnouncementAuthController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\CollaboratorEventController;
use App\Http\Controllers\CollaboratorProposalController;
use App\Http\Controllers\CollaboratorScheduleController;
use App\Http\Controllers\EventRegistrationController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', function () {
    $urls = [
        route('home'), route('about'), route('services'), route('events'), route('contact'),
        route('en.home'), route('en.about'), route('en.services'), route('en.events'), route('en.contact'),
        route('schedule.show'), route('en.schedule.show'),
    ];

    foreach (array_column(config('service_guides.pt', []), 'slug') as $slug) {
        $urls[] = route('resource.show', $slug);
        $urls[] = route('en.resource.show', $slug);
    }

    if (\Illuminate\Support\Facades\Schema::hasTable('company_events')) {
        foreach (\App\Models\CompanyEvent::query()->active()->pluck('slug') as $slug) {
            $urls[] = route('events.show', $slug);
            $urls[] = route('en.events.show', $slug);
        }
    }

    return response()
        ->view('sitemap', compact('urls'))
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/sobre-nos', [PageController::class, 'about'])->name('about');
Route::get('/servicos', [PageController::class, 'services'])->name('services');
Route::get('/recursos/{guide}', [PageController::class, 'resource'])->name('resource.show');
Route::get('/eventos', [PageController::class, 'events'])->name('events');
Route::get('/eventos/{event:slug}', [PageController::class, 'event'])->name('events.show');
Route::post('/eventos/{event:slug}/inscricao', [EventRegistrationController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('events.register');
Route::get('/agenda', [AppointmentController::class, 'show'])->name('schedule.show');
Route::get('/agenda/horarios', [AppointmentController::class, 'slots'])
    ->middleware('throttle:30,1')
    ->name('schedule.slots');
Route::post('/agenda', [AppointmentController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('schedule.store');
Route::get('/contactos', [PageController::class, 'contact'])->name('contact');
Route::post('/contactos', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

Route::get('/area-colaborador', [AnnouncementAuthController::class, 'showLogin'])->name('announcements.login');
Route::post('/area-colaborador', [AnnouncementAuthController::class, 'login'])
    ->middleware('throttle:6,1')
    ->name('announcements.login.store');
Route::get('/area-colaborador/restaurar-senha', [AnnouncementAuthController::class, 'showPasswordResetRequest'])->name('announcements.password.expired');
Route::post('/area-colaborador/restaurar-senha', [AnnouncementAuthController::class, 'sendPasswordResetLink'])
    ->middleware('throttle:6,1')
    ->name('announcements.password.expired.store');
Route::get('/area-colaborador/nova-senha/{token}', [AnnouncementAuthController::class, 'showPasswordResetForm'])->name('announcements.password.reset');
Route::post('/area-colaborador/nova-senha', [AnnouncementAuthController::class, 'updatePasswordFromToken'])
    ->middleware('throttle:6,1')
    ->name('announcements.password.update');
Route::post('/area-colaborador/sair', [AnnouncementAuthController::class, 'logout'])->name('announcements.logout');

Route::prefix('area-colaborador/anuncios')
    ->name('announcements.')
    ->middleware('announcement.admin')
    ->group(function (): void {
        Route::get('/', [AnnouncementController::class, 'index'])->name('dashboard');
        Route::post('/', [AnnouncementController::class, 'store'])->name('store');
        Route::patch('/{announcement}/estado', [AnnouncementController::class, 'toggle'])->name('toggle');
        Route::delete('/{announcement}', [AnnouncementController::class, 'destroy'])->name('destroy');
        Route::post('/acessos', [AnnouncementAdminController::class, 'store'])->name('admins.store');
        Route::delete('/acessos/{admin}', [AnnouncementAdminController::class, 'destroy'])->name('admins.destroy');
    });

Route::prefix('area-colaborador/eventos')
    ->name('collaborator.events.')
    ->middleware('announcement.admin')
    ->group(function (): void {
        Route::get('/', [CollaboratorEventController::class, 'index'])->name('index');
        Route::post('/', [CollaboratorEventController::class, 'store'])->name('store');
        Route::patch('/{event}', [CollaboratorEventController::class, 'update'])->name('update');
        Route::patch('/{event}/estado', [CollaboratorEventController::class, 'toggle'])->name('toggle');
        Route::patch('/inscricoes/{registration}', [CollaboratorEventController::class, 'updateRegistration'])->name('registrations.update');
    });

Route::prefix('area-colaborador/agenda')
    ->name('collaborator.schedule.')
    ->middleware('announcement.admin')
    ->group(function (): void {
        Route::get('/', [CollaboratorScheduleController::class, 'index'])->name('index');
        Route::patch('/configuracao', [CollaboratorScheduleController::class, 'updateSetting'])->name('setting.update');
        Route::post('/bloqueios', [CollaboratorScheduleController::class, 'storeBlock'])->name('blocks.store');
        Route::delete('/bloqueios/{block}', [CollaboratorScheduleController::class, 'destroyBlock'])->name('blocks.destroy');
        Route::patch('/marcacoes/{appointment}', [CollaboratorScheduleController::class, 'updateAppointment'])->name('appointments.update');
    });

Route::prefix('area-colaborador/propostas')
    ->name('collaborator.proposals.')
    ->middleware('announcement.admin')
    ->group(function (): void {
        Route::get('/', [CollaboratorProposalController::class, 'index'])->name('index');
        Route::post('/gerar', [CollaboratorProposalController::class, 'generate'])->name('generate');
    });

Route::prefix('en')->name('en.')->group(function (): void {
    Route::get('/', [PageController::class, 'homeEn'])->name('home');
    Route::get('/about', [PageController::class, 'aboutEn'])->name('about');
    Route::get('/services', [PageController::class, 'servicesEn'])->name('services');
    Route::get('/resources/{guide}', [PageController::class, 'resourceEn'])->name('resource.show');
    Route::get('/events', [PageController::class, 'eventsEn'])->name('events');
    Route::get('/events/{event:slug}', [PageController::class, 'eventEn'])->name('events.show');
    Route::post('/events/{event:slug}/registration', [EventRegistrationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('events.register');
    Route::get('/schedule', [AppointmentController::class, 'show'])->name('schedule.show');
    Route::get('/schedule/slots', [AppointmentController::class, 'slots'])
        ->middleware('throttle:30,1')
        ->name('schedule.slots');
    Route::post('/schedule', [AppointmentController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('schedule.store');
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
