<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Название')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state): void {
                        $set('slug', Str::slug((string) $state));
                    }),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                FileUpload::make('image')
                    ->label('Изображение')
                    ->image()
                    ->disk('public')
                    ->directory('categories')
                    ->visibility('public')
                    ->maxSize(5120),
                Select::make('columns')
                    ->label('Колонки на странице меню')
                    ->options([
                        2 => '2 колонки',
                        3 => '3 колонки',
                    ])
                    ->required()
                    ->default(3),
                TextInput::make('sort_order')
                    ->label('Порядок')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Toggle::make('is_active')
                    ->label('Активна')
                    ->default(true),
            ]);
    }
}
