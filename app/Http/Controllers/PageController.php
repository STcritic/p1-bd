<?php

namespace App\Http\Controllers;

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
        return $this->page('services', 'pt');
    }

    public function events(): View
    {
        return $this->page('events', 'pt');
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
        return $this->page('services', 'en');
    }

    public function eventsEn(): View
    {
        return $this->page('events', 'en');
    }

    public function contactEn(): View
    {
        return $this->page('contact', 'en');
    }

    private function page(string $view, string $locale): View
    {
        App::setLocale($locale);

        return view("pages.{$view}", compact('locale'));
    }
}
