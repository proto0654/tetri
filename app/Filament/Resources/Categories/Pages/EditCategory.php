<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Settings\SiteSettings;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Pboivin\FilamentPeek\Pages\Actions\PreviewAction;
use Pboivin\FilamentPeek\Pages\Concerns\HasPreviewModal;

class EditCategory extends EditRecord
{
    use HasPreviewModal;

    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PreviewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getPreviewModalView(): ?string
    {
        return 'menu';
    }

    protected function getPreviewModalDataRecordKey(): string
    {
        return 'category';
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutatePreviewModalData(array $data): array
    {
        return [
            'category' => $data['category'],
            'settings' => app(SiteSettings::class)->all(),
            'isPreview' => true,
        ];
    }
}
