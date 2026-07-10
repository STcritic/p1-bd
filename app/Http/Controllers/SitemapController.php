<?php

namespace App\Http\Controllers;

use App\Models\CompanyEvent;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [
            route('home'), route('about'), route('services'), route('events'), route('contact'),
            route('en.home'), route('en.about'), route('en.services'), route('en.events'), route('en.contact'),
            route('schedule.show'), route('en.schedule.show'),
        ];

        foreach (array_column(config('service_guides.pt', []), 'slug') as $slug) {
            $urls[] = route('resource.show', $slug);
            $urls[] = route('en.resource.show', $slug);
        }

        if (Schema::hasTable('company_events')) {
            foreach (CompanyEvent::query()->active()->pluck('slug') as $slug) {
                $urls[] = route('events.show', $slug);
                $urls[] = route('en.events.show', $slug);
            }
        }

        return response()
            ->view('sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }
}
