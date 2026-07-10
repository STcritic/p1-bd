<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\AnnouncementAdminController;
use App\Http\Controllers\AnnouncementAuthController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\CollaboratorEventController;
use App\Http\Controllers\CollaboratorOpportunityController;
use App\Http\Controllers\CollaboratorLanguageController;
use App\Http\Controllers\CollaboratorProposalController;
use App\Http\Controllers\CollaboratorScheduleController;
use App\Http\Controllers\DiagnosticPortalController;
use App\Http\Controllers\EventRegistrationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProposalVerificationController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

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

Route::get('/propostas/verificar/{token}', [ProposalVerificationController::class, 'show'])
    ->name('proposals.verify');
Route::get('/propostas/verificar/{token}/qr.svg', [ProposalVerificationController::class, 'qr'])
    ->name('proposals.verify.qr');

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
        Route::get('/',              [CollaboratorProposalController::class, 'index'])->name('index');
        Route::post('/gerar',        [CollaboratorProposalController::class, 'generate'])->name('generate');
        Route::get('/guardadas',           [CollaboratorProposalController::class, 'savedIndex'])->name('saved');
        Route::get('/{proposal}/editar',   [CollaboratorProposalController::class, 'edit'])->name('edit');
        Route::get('/{proposal}',          [CollaboratorProposalController::class, 'show'])->name('show');
        Route::patch('/{proposal}/estado',  [CollaboratorProposalController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{proposal}', [CollaboratorProposalController::class, 'destroy'])->name('destroy');
    });

/* ── Collaborator language toggle ───────────────────────────────────────── */
Route::post('/area-colaborador/idioma', CollaboratorLanguageController::class)
    ->middleware('announcement.admin')
    ->name('collaborator.set-language');

/* ── Consulting Workflow — Opportunities (Collaborator) ─────────────────── */
Route::prefix('area-colaborador/oportunidades')
    ->name('collaborator.opportunities.')
    ->middleware('announcement.admin')
    ->group(function (): void {
        Route::get('/',                                  [CollaboratorOpportunityController::class, 'index'])->name('index');
        Route::get('/nova',                              [CollaboratorOpportunityController::class, 'create'])->name('create');
        Route::post('/',                                 [CollaboratorOpportunityController::class, 'store'])->name('store');
        Route::get('/{opportunity}',                     [CollaboratorOpportunityController::class, 'show'])->name('show');
        Route::post('/{opportunity}/transicao',          [CollaboratorOpportunityController::class, 'transition'])->name('transition');
        Route::post('/{opportunity}/diagnostico',        [CollaboratorOpportunityController::class, 'sendDiagnostic'])->name('send-diagnostic');
        Route::post('/{opportunity}/contexto',           [CollaboratorOpportunityController::class, 'refreshContext'])->name('refresh-context');
        Route::post('/{opportunity}/nota',               [CollaboratorOpportunityController::class, 'addNote'])->name('add-note');
        Route::get('/{opportunity}/pre-proposta',                             [CollaboratorOpportunityController::class, 'preProposal'])->name('pre-proposal');
        Route::get('/{opportunity}/proposta',                                 [CollaboratorOpportunityController::class, 'generateProposal'])->name('generate-proposal');
        Route::get('/{opportunity}/documentos/{document}/download',           [CollaboratorOpportunityController::class, 'downloadDocument'])->name('document-download');
        Route::delete('/{opportunity}',                                       [CollaboratorOpportunityController::class, 'destroy'])->name('destroy');
    });

/* ── Diagnostic Client Portal (public — token-only auth) ────────────────── */
Route::prefix('diagnostico')
    ->name('diagnostic.')
    ->group(function (): void {
        Route::get('/{token}',          [DiagnosticPortalController::class, 'show'])->name('portal');
        Route::post('/{token}/guardar', [DiagnosticPortalController::class, 'save'])->name('save');
        Route::post('/{token}/submeter',[DiagnosticPortalController::class, 'submit'])->name('submit');
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
