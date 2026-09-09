<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class MenuGrid extends Component
{
    use WithPagination;

    public ?int $activeCategoryId = null;

    public function mount(?string $categorySlug = null): void
    {
        if (! filled($categorySlug)) {
            return;
        }

        $matched = Category::query()
            ->active()
            ->where('slug', $categorySlug)
            ->first();

        if ($matched) {
            $this->activeCategoryId = $matched->id;
        }
    }

    public function showAll(): void
    {
        $this->resetPage();
        $this->activeCategoryId = null;
        $this->syncBrowserUrl(route('menu'));
    }

    public function setCategory(int $id): void
    {
        $slug = Category::query()->active()->whereKey($id)->value('slug');

        if (! filled($slug)) {
            return;
        }

        $this->resetPage();
        $this->activeCategoryId = $id;
        $this->syncBrowserUrl(route('menu.category', $slug));
    }

    public function render(): View
    {
        /** @var Collection<int, Category> $categories */
        $categories = Category::query()->active()->ordered()->get();

        if ($this->activeCategoryId === null) {
            $categories->load([
                'menuItems' => fn ($query) => $query->active()->ordered(),
            ]);

            return view('livewire.menu-grid', [
                'categories' => $categories,
                'activeCategory' => null,
                'items' => null,
            ]);
        }

        $activeCategory = $categories->firstWhere('id', $this->activeCategoryId);

        if (! $activeCategory) {
            $this->activeCategoryId = null;

            $categories->load([
                'menuItems' => fn ($query) => $query->active()->ordered(),
            ]);

            return view('livewire.menu-grid', [
                'categories' => $categories,
                'activeCategory' => null,
                'items' => null,
            ]);
        }

        /** @var LengthAwarePaginator<int, MenuItem> $items */
        $items = MenuItem::query()
            ->where('category_id', $activeCategory->id)
            ->active()
            ->ordered()
            ->paginate(12);

        return view('livewire.menu-grid', [
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'items' => $items,
        ]);
    }

    protected function syncBrowserUrl(string $url): void
    {
        $this->js('window.history.pushState({}, "", '.json_encode($url).')');
    }
}
