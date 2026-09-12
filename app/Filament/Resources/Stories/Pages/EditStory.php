<?php

namespace App\Filament\Resources\Stories\Pages;

use App\Filament\Resources\Stories\StoryResource;
use App\Models\Category;
use App\Models\Story;
use App\Settings\SiteSettings;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Pboivin\FilamentPeek\Pages\Actions\PreviewAction;
use Pboivin\FilamentPeek\Pages\Concerns\HasPreviewModal;

class EditStory extends EditRecord
{
    use HasPreviewModal;

    protected static string $resource = StoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PreviewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getPreviewModalView(): ?string
    {
        return 'home';
    }

    protected function getPreviewModalDataRecordKey(): string
    {
        return 'story';
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutatePreviewModalData(array $data): array
    {
        /** @var Story $story */
        $story = $data['story'];

        $stories = Story::query()
            ->active()
            ->ordered()
            ->get()
            ->map(fn (Story $existing): Story => $existing->is($story) ? $story : $existing);

        if (! $stories->contains(fn (Story $existing): bool => $existing->is($story))) {
            $stories->push($story);
        }

        return [
            'settings' => app(SiteSettings::class)->all(),
            'categories' => Category::query()->active()->ordered()->get(),
            'stories' => $stories,
            'isPreview' => true,
        ];
    }
}
