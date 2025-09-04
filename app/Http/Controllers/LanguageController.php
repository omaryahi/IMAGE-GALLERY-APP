<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switch(Request $request, $lang)
    {
        if (in_array($lang, ['en', 'ar'])) {
            Session::put('locale', $lang);
            App::setLocale($lang);
        }

        return response()->json(['success' => true]);
    }
}
