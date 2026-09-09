<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Settings\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function __invoke(Request $request, SiteSettings $settings, ?Category $category = null): View|RedirectResponse
    {
        if ($category === null && $request->filled('category')) {
            $matched = Category::query()
                ->active()
                ->where('slug', $request->string('category')->toString())
                ->first();

            if ($matched) {
                return redirect()->route('menu.category', $matched);
            }
        }

        if ($category !== null) {
            abort_unless($category->is_active, 404);
        }

        return view('menu', [
            'category' => $category,
            'settings' => $settings->all(),
        ]);
    }
}
