<?php

namespace App\Support;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class NavLinkFieldSchema
{
    /**
     * @return array<int, Select|TextInput>
     */
    public static function fields(): array
    {
        return [
            TextInput::make('label')
                ->label('Текст')
                ->required(),
            Select::make('type')
                ->label('Тип')
                ->options([
                    'link' => 'Ссылка',
                    'booking' => 'Бронирование',
                ])
                ->default('link')
                ->required()
                ->live(),
            TextInput::make('url')
                ->label('Ссылка')
                ->visible(fn (Get $get): bool => ($get('type') ?? 'link') === 'link')
                ->required(fn (Get $get): bool => ($get('type') ?? 'link') === 'link')
                ->helperText('Например /#concept, /menu или #contacts'),
            TextInput::make('booking_source')
                ->label('Источник бронирования')
                ->visible(fn (Get $get): bool => $get('type') === 'booking')
                ->helperText('Латиница, напр. banket → nav-banket / footer-banket. Пусто → slug из текста.'),
        ];
    }

    public static function repeater(string $name = 'nav_links'): Repeater
    {
        return Repeater::make($name)
            ->label('Пункты меню')
            ->schema(self::fields())
            ->reorderable()
            ->collapsible()
            ->defaultItems(0)
            ->columnSpanFull();
    }
}
