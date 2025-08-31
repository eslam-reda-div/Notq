<?php

namespace App\Filament\Admin\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->columnSpanFull()
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->unique(ignorable: fn ($record) => $record)
                    ->columnSpanFull()
                    ->default(null),
                FileUpload::make('avatar_url')
                    ->image()
                    ->directory('customers')
                    ->imageEditor()
                    ->imagePreviewHeight('250')
                    ->panelAspectRatio('7:2')
                    ->panelLayout('integrated')
                    ->columnSpan('full'),
                TextInput::make('password')
                    ->password()
                    ->columnSpanFull()
                    ->required(),
            ]);
    }
}
