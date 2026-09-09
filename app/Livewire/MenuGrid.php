<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class MenuGrid extends Component
{
    public ?int $activeCategoryId = null;

    public function mount(): void
    {
        $this->activeCategoryId = Category::query()
            ->active()
            ->ordered()
            ->value('id');
    }

    public function setCategory(int $id): void
    {
        $this->activeCategoryId = $id;
    }

    public function render(): View
    {
        /** @var Collection<int, Category> $categories */
        $categories = Category::query()->active()->ordered()->get();

        $activeCategory = $categories->firstWhere('id', $this->activeCategoryId) ?? $categories->first();

        if ($activeCategory && $this->activeCategoryId !== $activeCategory->id) {
            $this->activeCategoryId = $activeCategory->id;
        }

        $items = $activeCategory
            ? MenuItem::query()
                ->where('category_id', $activeCategory->id)
                ->active()
                ->ordered()
                ->get()
            : collect();

        return view('livewire.menu-grid', [
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'items' => $items,
        ]);
    }
}
