<?php

namespace App\Http\Controllers;

use App\Settings\SiteSettings;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function __invoke(SiteSettings $settings): View
    {
        return view('menu', [
            'settings' => $settings->all(),
        ]);
    }
}
