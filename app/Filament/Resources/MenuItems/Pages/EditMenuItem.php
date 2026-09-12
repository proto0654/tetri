<?php

namespace App\Filament\Resources\MenuItems\Pages;

use App\Filament\Resources\MenuItems\MenuItemResource;
use App\Models\Category;
use App\Models\MenuItem;
use App\Settings\SiteSettings;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Pboivin\FilamentPeek\Pages\Actions\PreviewAction;
use Pboivin\FilamentPeek\Pages\Concerns\HasPreviewModal;

class EditMenuItem extends EditRecord
{
    use HasPreviewModal;

    protected static string $resource = MenuItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PreviewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getPreviewModalView(): ?string
    {
        return 'menu.show';
    }

    protected function getPreviewModalDataRecordKey(): string
    {
        return 'item';
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutatePreviewModalData(array $data): array
    {
        /** @var MenuItem $item */
        $item = $data['item'];

        $category = $item->category_id
            ? Category::query()->find($item->category_id)
            : null;

        if ($category !== null) {
            $item->setRelation('category', $category);
        }

        $related = MenuItem::query()
            ->when(
                $item->category_id,
                fn ($query) => $query->where('category_id', $item->category_id),
            )
            ->active()
            ->when(
                $item->getKey(),
                fn ($query) => $query->whereKeyNot($item->getKey()),
            )
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return [
            'item' => $item,
            'category' => $category ?? new Category,
            'related' => $related,
            'settings' => app(SiteSettings::class)->all(),
            'isPreview' => true,
        ];
    }
}
