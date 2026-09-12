<?php

namespace App\Filament\Resources\Stories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class StoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Название')
                    ->maxLength(255),
                FileUpload::make('video_path')
                    ->label('Видео')
                    ->acceptedFileTypes(['video/mp4', 'video/webm'])
                    ->disk('public')
                    ->directory('stories')
                    ->visibility('public')
                    ->maxSize(30720),
                FileUpload::make('preview_image')
                    ->label('Превью')
                    ->image()
                    ->disk('public')
                    ->directory('stories/previews')
                    ->visibility('public')
                    ->maxSize(5120)
                    ->requiredWithout('video_path'),
                TextInput::make('sort_order')
                    ->label('Порядок')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Toggle::make('is_active')
                    ->label('Активен')
                    ->default(true),
            ]);
    }
}
