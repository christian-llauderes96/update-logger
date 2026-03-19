<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function canViewAny(): bool
    {
        // Only users with the 'admin' role can see the "Users" link
        return auth()->user()->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            TextInput::make('name')->required(),
            TextInput::make('username')
                ->label('Username')
                ->unique(ignoreRecord: true)
                ->regex('/^[a-zA-Z0-9_]+$/') // Allows letters, numbers, and underscores
                ->validationMessages([
                    'regex' => 'The username can only contain letters, numbers, and underscores.',
                ])
                ->maxLength(255)
                ->placeholder('e.g., llauderes_cjb'),
            TextInput::make('email')->email()->required(),
            Select::make('role')
                ->options([
                    'admin' => 'Super Admin',
                    'developer' => 'Developer',
                ])
                ->required()
                ->native(false),
            TextInput::make('password')
                ->password()
                ->label('Password')
                // 1. Only hash the password if there is actually a value typed in
                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                
                // 2. Only include this field in the database update if it has a value
                ->dehydrated(fn ($state) => filled($state))
                
                // 3. Make it REQUIRED on the 'create' page, but OPTIONAL on the 'edit' page
                ->required(fn (string $context): bool => $context === 'create')
                
                ->placeholder(fn (string $context): bool => 
                    $context === 'edit' ? 'Leave blank to keep current password' : 'Enter a secure password'
                ),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            // User's Full Name & Avatar (Filament handles avatars automatically if you use them)
            Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->sortable(),

            // Username with a unique style
            Tables\Columns\TextColumn::make('username')
                ->label('ID / Username')
                ->badge()
                ->color('gray')
                ->placeholder('Not set')
                ->searchable(),

            // Email with a "copy to clipboard" feature
            Tables\Columns\TextColumn::make('email')
                ->icon('heroicon-m-envelope')
                ->copyable()
                ->searchable(),

            // Role with color-coded badges
            Tables\Columns\TextColumn::make('role')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'admin' => 'danger',     // Red for Admin
                    'developer' => 'info',   // Blue for Dev
                    default => 'gray',
                })
                ->formatStateUsing(fn (string $state): string => ucfirst($state)), // Capitalizes 'admin' to 'Admin'

            // When they joined the system
            Tables\Columns\TextColumn::make('created_at')
                ->label('Joined')
                ->dateTime('M d, Y')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            // Filter by Role (Admin vs Developer)
            Tables\Filters\SelectFilter::make('role')
                ->options([
                    'admin' => 'Admins',
                    'developer' => 'Developers',
                ]),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
