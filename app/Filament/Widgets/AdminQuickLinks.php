<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\ManageSiteSettings;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\MenuItems\MenuItemResource;
use App\Filament\Resources\Stories\StoryResource;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminQuickLinks extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 1;

    protected ?string $heading = 'Быстрый переход';

    /**
     * @var int | array<string, ?int> | null
     */
    protected int|array|null $columns = 2;

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        return [
            Stat::make('Меню', 'Категории')
                ->icon(Heroicon::OutlinedRectangleStack)
                ->url(CategoryResource::getUrl('index'))
                ->color('primary'),
            Stat::make('Меню', 'Блюда')
                ->icon(Heroicon::OutlinedShoppingBag)
                ->url(MenuItemResource::getUrl('index'))
                ->color('primary'),
            Stat::make('Контент', 'Сторисы')
                ->icon(Heroicon::OutlinedFilm)
                ->url(StoryResource::getUrl('index'))
                ->color('primary'),
            Stat::make('Контент', 'Настройки сайта')
                ->icon(Heroicon::OutlinedCog6Tooth)
                ->url(ManageSiteSettings::getUrl())
                ->color('primary'),
        ];
    }
}
