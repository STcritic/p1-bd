<?php

namespace App\Http\Controllers;

use App\Models\CompanyEvent;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;

class PageController extends Controller
{
    public function home(): View
    {
        return $this->page('home', 'pt');
    }

    public function about(): View
    {
        return $this->page('about', 'pt');
    }

    public function services(): View
    {
        return $this->page('services', 'pt', [
            'guides' => $this->guides('pt'),
        ]);
    }

    public function events(): View
    {
        return $this->eventsPage('pt');
    }

    public function contact(): View
    {
        return $this->page('contact', 'pt');
    }

    public function homeEn(): View
    {
        return $this->page('home', 'en');
    }

    public function aboutEn(): View
    {
        return $this->page('about', 'en');
    }

    public function servicesEn(): View
    {
        return $this->page('services', 'en', [
            'guides' => $this->guides('en'),
        ]);
    }

    public function eventsEn(): View
    {
        return $this->eventsPage('en');
    }

    public function contactEn(): View
    {
        return $this->page('contact', 'en');
    }

    public function resource(string $guide): View
    {
        return $this->resourcePage($guide, 'pt');
    }

    public function resourceEn(string $guide): View
    {
        return $this->resourcePage($guide, 'en');
    }

    public function event(CompanyEvent $event): View
    {
        return $this->eventPage($event, 'pt');
    }

    public function eventEn(CompanyEvent $event): View
    {
        return $this->eventPage($event, 'en');
    }

    private function page(string $view, string $locale, array $data = []): View
    {
        App::setLocale($locale);

        return view("pages.{$view}", ['locale' => $locale, ...$data]);
    }

    private function resourcePage(string $slug, string $locale): View
    {
        App::setLocale($locale);

        $guide = collect($this->guides($locale))->firstWhere('slug', $slug);

        abort_if(! $guide, 404);

        return view('pages.resource', [
            'locale' => $locale,
            'guide' => $guide,
            'guides' => $this->guides($locale),
        ]);
    }

    private function eventsPage(string $locale): View
    {
        App::setLocale($locale);

        return view('pages.events', [
            'locale' => $locale,
            'events' => CompanyEvent::query()
                ->active()
                ->upcoming()
                ->withCount('registrations')
                ->orderByDesc('is_featured')
                ->orderBy('starts_at')
                ->latest()
                ->get(),
            'pastEvents' => CompanyEvent::query()
                ->active()
                ->past()
                ->withCount('registrations')
                ->orderByDesc('starts_at')
                ->limit(8)
                ->get(),
        ]);
    }

    private function eventPage(CompanyEvent $event, string $locale): View
    {
        App::setLocale($locale);
        abort_unless($event->is_active, 404);

        return view('pages.event-show', [
            'locale' => $locale,
            'event' => $event->loadCount('registrations'),
        ]);
    }

    private function guides(string $locale): array
    {
        return config("service_guides.{$locale}", []);
    }
}
