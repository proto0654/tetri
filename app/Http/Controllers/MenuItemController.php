<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use App\Settings\SiteSettings;
use Illuminate\View\View;

class MenuItemController extends Controller
{
    public function __invoke(Category $category, MenuItem $menuItem, SiteSettings $settings): View
    {
        abort_unless($category->is_active && $menuItem->is_active, 404);

        $related = MenuItem::query()
            ->where('category_id', $category->id)
            ->active()
            ->whereKeyNot($menuItem->id)
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('menu.show', [
            'category' => $category,
            'item' => $menuItem,
            'related' => $related,
            'settings' => $settings->all(),
        ]);
    }
}
