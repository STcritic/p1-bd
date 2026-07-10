<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CollaboratorLanguageController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $lang = $request->string('lang')->toString() ?: 'pt';

        Session::put('collab_lang', in_array($lang, ['pt', 'en'], true) ? $lang : 'pt');

        return back();
    }
}
