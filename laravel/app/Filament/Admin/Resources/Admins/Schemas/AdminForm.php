<?php

namespace App\Filament\Admin\Resources\Admins\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class AdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->minLength(2)
                    ->maxLength(255)
                    ->columnSpan('full')
                    ->required(),

                Forms\Components\FileUpload::make('avatar_url')
                    ->image()
                    ->directory('admin')
                    ->imageEditor()
                    ->imagePreviewHeight('250')
                    ->panelAspectRatio('7:2')
                    ->panelLayout('integrated')
                    ->columnSpan('full'),

                Forms\Components\TextInput::make('email')
                    ->required()
                    ->prefixIcon('heroicon-m-envelope')
                    ->unique(ignorable: fn ($record) => $record)
                    ->columnSpan('full')
                    ->email(),

                Forms\Components\TextInput::make('password')
                    ->password()
                    ->confirmed()
                    ->columnSpan(1)
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create'),

                Forms\Components\TextInput::make('password_confirmation')
                    ->required(fn (string $context): bool => $context === 'create')
                    ->columnSpan(1)
                    ->password(),
            ]);
    }
}
