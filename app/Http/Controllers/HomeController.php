<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Story;
use App\Settings\SiteSettings;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(SiteSettings $settings): View
    {
        return view('home', [
            'settings' => $settings->all(),
            'categories' => Category::query()->active()->ordered()->get(),
            'stories' => Story::query()->active()->ordered()->get(),
        ]);
    }
}
