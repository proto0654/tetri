<?php

namespace App\Support;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class IconFieldSchema
{
    /**
     * Shared icon picker: Heroicons with SVG preview + optional custom SVG upload.
     *
     * @return array<int, Select|FileUpload>
     */
    public static function fields(
        bool $iconRequiredWhenNoUpload = true,
        string $directory = 'site/icons',
    ): array {
        return [
            Select::make('icon')
                ->label('Иконка из набора')
                ->options(fn (): array => HeroiconOptions::outlinedWithPreview())
                ->searchable()
                ->allowHtml()
                ->getOptionLabelUsing(fn (?string $value): ?string => HeroiconOptions::previewLabel($value))
                ->helperText('Поиск по названию. Если нужной нет — загрузите SVG ниже.')
                ->required(fn (Get $get): bool => $iconRequiredWhenNoUpload && blank($get('custom_icon')))
                ->dehydrated(true),
            FileUpload::make('custom_icon')
                ->label('Или свой SVG')
                ->acceptedFileTypes(['image/svg+xml', 'image/svg', '.svg'])
                ->disk('public')
                ->directory($directory)
                ->visibility('public')
                ->maxSize(512)
                ->helperText('Приоритетнее набора, если файл загружен.'),
        ];
    }

    /**
     * @return array<int, Select|FileUpload|TextInput>
     */
    public static function withUrl(
        bool $urlRequired = true,
        string $directory = 'site/icons',
    ): array {
        return [
            ...self::fields(directory: $directory),
            TextInput::make('url')
                ->label('Ссылка')
                ->required($urlRequired)
                ->helperText($urlRequired ? 'Например #menu-preview, tel:+7… или https://…' : 'Необязательно'),
        ];
    }

    /**
     * @return array<int, Select|FileUpload|TextInput>
     */
    public static function withTextAndUrl(
        bool $urlRequired = true,
        string $directory = 'site/icons',
    ): array {
        return [
            ...self::fields(directory: $directory),
            TextInput::make('text')
                ->label('Текст')
                ->required(),
            TextInput::make('url')
                ->label('Ссылка')
                ->required($urlRequired),
        ];
    }
}
