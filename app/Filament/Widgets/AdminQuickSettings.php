<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\ManageSiteSettings;
use App\Settings\SiteSettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Widgets\Widget;
use Illuminate\Support\Arr;

class AdminQuickSettings extends Widget implements HasSchemas
{
    use InteractsWithSchemas;

    protected static bool $isLazy = false;

    protected static ?int $sort = 2;

    /**
     * @var view-string
     */
    protected string $view = 'filament.widgets.admin-quick-settings';

    /**
     * @var int | string | array<string, int | string | null>
     */
    protected int|string|array $columnSpan = 'full';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    /**
     * @var list<string>
     */
    private const QUICK_KEYS = [
        'hero_title',
        'hero_background_image',
        'address',
        'phones',
        'working_hours',
        'seo_title',
        'seo_description',
        'seo_title_suffix',
        'home_seo_title',
        'home_seo_description',
        'menu_seo_title',
    ];

    public function mount(SiteSettings $settings): void
    {
        $this->form->fill(Arr::only($settings->all(), self::QUICK_KEYS));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Контакты')
                    ->schema([
                        TextInput::make('address')
                            ->label('Адрес')
                            ->columnSpanFull(),
                        Repeater::make('phones')
                            ->label('Телефоны')
                            ->schema([
                                TextInput::make('number')
                                    ->label('Номер')
                                    ->required(),
                            ])
                            ->columnSpanFull(),
                        TextInput::make('working_hours')
                            ->label('Часы работы')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Hero')
                    ->schema([
                        TextInput::make('hero_title')
                            ->label('Заголовок')
                            ->required(),
                        FileUpload::make('hero_background_image')
                            ->label('Фон')
                            ->image()
                            ->disk('public')
                            ->directory('site/hero')
                            ->visibility('public')
                            ->maxSize(8192),
                    ])
                    ->columns(2),
                Section::make('SEO')
                    ->schema([
                        TextInput::make('seo_title')
                            ->label('Title по умолчанию')
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('seo_description')
                            ->label('Description по умолчанию')
                            ->rows(2)
                            ->columnSpanFull(),
                        TextInput::make('seo_title_suffix')
                            ->label('Суффикс title («— …»)')
                            ->required(),
                        TextInput::make('menu_seo_title')
                            ->label('Меню — сегмент title')
                            ->required(),
                        TextInput::make('home_seo_title')
                            ->label('Главная — title')
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('home_seo_description')
                            ->label('Главная — description')
                            ->rows(2)
                            ->helperText('Пусто → общий description.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(SiteSettings $settings): void
    {
        $settings->save($this->form->getState());

        Notification::make()
            ->title('Быстрые настройки сохранены')
            ->success()
            ->send();
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'allSettingsUrl' => ManageSiteSettings::getUrl(),
        ];
    }
}
