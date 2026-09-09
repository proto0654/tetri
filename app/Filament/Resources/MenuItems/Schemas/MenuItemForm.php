<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MenuItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('Категория')
                    ->relationship('category', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('title')
                    ->label('Название')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Описание')
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->label('Цена')
                    ->numeric()
                    ->prefix('₽')
                    ->required(),
                FileUpload::make('image')
                    ->label('Изображение')
                    ->image()
                    ->disk('public')
                    ->directory('menu-items')
                    ->visibility('public')
                    ->maxSize(5120),
                TextInput::make('sort_order')
                    ->label('Порядок')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Toggle::make('is_active')
                    ->label('Активно')
                    ->default(true),
            ]);
    }
}
