<?php

namespace App\Http\Controllers;

use App\Settings\SiteSettings;
use Illuminate\View\View;

class PrivacyController extends Controller
{
    public function __invoke(SiteSettings $settings): View
    {
        return view('privacy', [
            'settings' => $settings->all(),
        ]);
    }
}
