<?php

namespace App\Filament\Pages;

use App\Settings\SiteSettings;
use App\Support\IconFieldSchema;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Fieldset;
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
                                Fieldset::make('Градиент оверлея')
                                    ->schema([
                                        ColorPicker::make('hero_overlay_from')
                                            ->label('Сверху')
                                            ->rgba()
                                            ->required(),
                                        ColorPicker::make('hero_overlay_via')
                                            ->label('Середина')
                                            ->rgba()
                                            ->required(),
                                        ColorPicker::make('hero_overlay_to')
                                            ->label('Снизу')
                                            ->rgba()
                                            ->required()
                                            ->helperText('По умолчанию cream → фон следующей секции'),
                                    ])
                                    ->columns(3),
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
                                Textarea::make('kids_title')
                                    ->label('Заголовок')
                                    ->rows(2)
                                    ->required()
                                    ->helperText('Перенос строки: Enter или тег <br>')
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
                                Textarea::make('menu_section_title')
                                    ->label('Заголовок секции')
                                    ->rows(2)
                                    ->helperText('Перенос строки: Enter или тег <br>'),
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
                                Textarea::make('concept_title')
                                    ->label('Заголовок')
                                    ->rows(2)
                                    ->helperText('Перенос строки: Enter или тег <br>')
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
                                Textarea::make('stories_section_title')
                                    ->label('Заголовок секции')
                                    ->rows(2)
                                    ->helperText('Перенос строки: Enter или тег <br>'),
                                TextInput::make('stories_section_aside')
                                    ->label('Правая пометка, строка 1'),
                                TextInput::make('stories_section_aside_note')
                                    ->label('Правая пометка, строка 2'),
                            ]),
                        Tab::make('Контакты')
                            ->schema([
                                Textarea::make('contacts_title')
                                    ->label('Заголовок')
                                    ->rows(2)
                                    ->helperText('Перенос строки: Enter или тег <br>'),
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
                                TextInput::make('map_latitude')
                                    ->label('Широта метки')
                                    ->numeric()
                                    ->step('any'),
                                TextInput::make('map_longitude')
                                    ->label('Долгота метки')
                                    ->numeric()
                                    ->step('any'),
                                TextInput::make('map_marker_label')
                                    ->label('Текст метки на карте'),
                                Textarea::make('map_embed_url')
                                    ->label('URL карты (iframe src)')
                                    ->rows(2)
                                    ->helperText('Если пусто — iframe собирается из широты, долготы и текста метки.')
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Подвал и CTA')
                            ->schema([
                                FileUpload::make('favicon')
                                    ->label('Favicon')
                                    ->acceptedFileTypes([
                                        'image/png',
                                        'image/x-icon',
                                        'image/vnd.microsoft.icon',
                                        'image/svg+xml',
                                        'image/webp',
                                        'image/jpeg',
                                    ])
                                    ->disk('public')
                                    ->directory('site/favicon')
                                    ->visibility('public')
                                    ->maxSize(1024)
                                    ->helperText('Квадрат, лучше PNG/SVG, минимум 48×48 px (Google).')
                                    ->columnSpanFull(),
                                FileUpload::make('logo')
                                    ->label('Логотип')
                                    ->acceptedFileTypes([
                                        'image/png',
                                        'image/svg+xml',
                                        'image/webp',
                                        'image/jpeg',
                                    ])
                                    ->disk('public')
                                    ->directory('site/logo')
                                    ->visibility('public')
                                    ->maxSize(2048)
                                    ->helperText('Шапка и подвал; если пусто — текст ТЕТРИ.')
                                    ->columnSpanFull(),
                                FileUpload::make('og_image')
                                    ->label('OG-картинка')
                                    ->image()
                                    ->acceptedFileTypes([
                                        'image/png',
                                        'image/jpeg',
                                        'image/webp',
                                    ])
                                    ->disk('public')
                                    ->directory('site/og')
                                    ->visibility('public')
                                    ->maxSize(5120)
                                    ->helperText('Рекомендуемо 1200×630 для репостов.')
                                    ->columnSpanFull(),
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
                                Textarea::make('cookie_notice')
                                    ->label('Уведомление о cookie')
                                    ->rows(2)
                                    ->helperText('Текст в полосе копирайта. Вставьте {privacy} — на этом месте будет ссылка с заголовком политики.')
                                    ->columnSpanFull(),
                                TextInput::make('privacy_title')
                                    ->label('Заголовок политики')
                                    ->columnSpanFull(),
                                Textarea::make('privacy_body')
                                    ->label('Текст политики')
                                    ->rows(14)
                                    ->helperText('Абзацы разделяйте пустой строкой.')
                                    ->columnSpanFull(),
                                TextInput::make('design_credit')
                                    ->label('Design by'),
                            ]),
                        Tab::make('Интеграция с мессенджером MAX')
                            ->schema([
                                TextInput::make('max_bot_token')
                                    ->label('Токен бота MAX')
                                    ->password()
                                    ->revealable()
                                    ->required()
                                    ->columnSpanFull(),
                                TextInput::make('max_chat_id')
                                    ->label('ID чата группы MAX')
                                    ->required()
                                    ->helperText('Числовой ID группового чата, куда бот отправляет заявки.')
                                    ->columnSpanFull(),
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
