<?php

namespace App\Filament\Pages;

use App\Settings\SiteSettings;
use App\Support\IconFieldSchema;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageSiteSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Настройки сайта';

    protected static ?string $title = 'Настройки сайта';

    protected static string|\UnitEnum|null $navigationGroup = 'Контент';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.manage-site-settings';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(SiteSettings $settings): void
    {
        $this->form->fill($settings->all());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Settings')
                    ->tabs([
                        Tab::make('Hero')
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
                                FileUpload::make('hero_video_path')
                                    ->label('Центральное видео')
                                    ->acceptedFileTypes(['video/mp4', 'video/webm'])
                                    ->disk('public')
                                    ->directory('site/hero')
                                    ->visibility('public')
                                    ->maxSize(30720)
                                    ->helperText('MP4 или WebM, до 30 МБ. Для hero лучше короткий muted-ролик.'),
                                FileUpload::make('hero_video_preview')
                                    ->label('Превью видео')
                                    ->image()
                                    ->disk('public')
                                    ->directory('site/hero')
                                    ->visibility('public')
                                    ->maxSize(5120),
                                Repeater::make('hero_icons')
                                    ->label('Иконки')
                                    ->schema(IconFieldSchema::withUrl())
                                    ->columns(2)
                                    ->defaultItems(3)
                                    ->maxItems(6)
                                    ->reorderable(),
                            ]),
                        Tab::make('Дети и родители')
                            ->schema([
                                TextInput::make('kids_eyebrow')
                                    ->label('Надзаголовок (◇)')
                                    ->helperText('Декоративная строка над заголовком'),
                                TextInput::make('kids_title')
                                    ->label('Заголовок')
                                    ->required()
                                    ->columnSpanFull(),
                                Repeater::make('kids_benefits')
                                    ->label('Бенефиты')
                                    ->schema(IconFieldSchema::withTextAndUrl(urlRequired: false))
                                    ->columns(2)
                                    ->columnSpanFull(),
                                FileUpload::make('kids_images')
                                    ->label('Галерея (2 фото)')
                                    ->image()
                                    ->multiple()
                                    ->maxFiles(2)
                                    ->reorderable()
                                    ->disk('public')
                                    ->directory('site/kids')
                                    ->visibility('public')
                                    ->maxSize(5120)
                                    ->columnSpanFull(),
                                Textarea::make('kids_description')
                                    ->label('Описание (абзац 1)')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                Textarea::make('kids_description_secondary')
                                    ->label('Описание (абзац 2)')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                TextInput::make('kids_location_note')
                                    ->label('Нижняя пометка (◇ адрес)')
                                    ->columnSpanFull(),
                                TextInput::make('kids_cta_label')
                                    ->label('Текст кнопки'),
                            ])
                            ->columns(2),
                        Tab::make('Меню (тексты)')
                            ->schema([
                                TextInput::make('menu_section_eyebrow')
                                    ->label('Надзаголовок (◇) — главная и /menu')
                                    ->columnSpanFull(),
                                TextInput::make('menu_section_title')
                                    ->label('Заголовок секции'),
                                Textarea::make('menu_section_description')
                                    ->label('Описание на главной')
                                    ->rows(2)
                                    ->columnSpanFull(),
                                TextInput::make('menu_section_cta_label')
                                    ->label('Текст кнопки на главной'),
                                TextInput::make('menu_page_meta')
                                    ->label('/menu — правая пометка, строка 1'),
                                TextInput::make('menu_page_meta_note')
                                    ->label('/menu — правая пометка, строка 2'),
                            ]),
                        Tab::make('Концепция')
                            ->schema([
                                TextInput::make('concept_eyebrow')
                                    ->label('Надзаголовок (◇)')
                                    ->columnSpanFull(),
                                TextInput::make('concept_title')
                                    ->label('Заголовок')
                                    ->columnSpanFull(),
                                TextInput::make('concept_aside')
                                    ->label('Боковая пометка (◇)')
                                    ->columnSpanFull(),
                                Textarea::make('concept_description')
                                    ->label('Описание')
                                    ->rows(5)
                                    ->columnSpanFull(),
                                TextInput::make('concept_cta_label')
                                    ->label('Текст кнопки'),
                            ]),
                        Tab::make('Сторисы')
                            ->schema([
                                TextInput::make('stories_section_title')
                                    ->label('Заголовок секции'),
                                TextInput::make('stories_section_aside')
                                    ->label('Правая пометка, строка 1'),
                                TextInput::make('stories_section_aside_note')
                                    ->label('Правая пометка, строка 2'),
                            ]),
                        Tab::make('Контакты')
                            ->schema([
                                TextInput::make('contacts_title')
                                    ->label('Заголовок'),
                                TextInput::make('address')
                                    ->label('Адрес'),
                                Repeater::make('phones')
                                    ->label('Телефоны')
                                    ->schema([
                                        TextInput::make('number')
                                            ->label('Номер')
                                            ->required(),
                                    ])
                                    ->columnSpanFull(),
                                TextInput::make('working_hours')
                                    ->label('Часы работы'),
                                Textarea::make('map_embed_url')
                                    ->label('URL карты (iframe src)')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Подвал и CTA')
                            ->schema([
                                Textarea::make('footer_about')
                                    ->label('Описание в подвале')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                Repeater::make('social_links')
                                    ->label('Соцсети')
                                    ->schema(IconFieldSchema::withTextAndUrl())
                                    ->columns(2)
                                    ->columnSpanFull(),
                                TextInput::make('booking_cta_label')
                                    ->label('Текст бронирования'),
                                TextInput::make('booking_cta_url')
                                    ->label('Ссылка бронирования (устарело)')
                                    ->helperText('Кнопки брони открывают попап; поле можно оставить пустым.'),
                                TextInput::make('copyright')
                                    ->label('Копирайт'),
                                TextInput::make('design_credit')
                                    ->label('Design by'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(SiteSettings $settings): void
    {
        $settings->save($this->form->getState());

        Notification::make()
            ->title('Настройки сохранены')
            ->success()
            ->send();
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Сохранить')
                ->action('save'),
        ];
    }
}
