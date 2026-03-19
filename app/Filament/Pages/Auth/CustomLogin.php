<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class CustomLogin extends Login
{
    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema([
                    $this->getLoginFormComponent(), // This is the Username field
                    $this->getPasswordFormComponent(),
                    $this->getRememberFormComponent(),
                ])
                ->statePath('data'),
        ];
    }

    protected function getLoginFormComponent(): Component
    {
        return TextInput::make('username') // Matches your database column
            ->label('Email or Username')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }
    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/login.form.password.label'))
            ->hint(filament()->hasPasswordReset() ? new HtmlString(Blade::render('<x-filament::link :href="filament()->getResetPasswordUrl()"> {{ __(\'filament-panels::pages/auth/login.actions.request_password_reset.label\') }} </x-filament::link>')) : null)
            ->password() // <--- THIS IS THE CRITICAL LINE
            ->revealable() // Optional: Adds an eye icon to show/hide the password
            ->autocomplete('current-password')
            ->required()
            ->extraInputAttributes(['tabindex' => 2]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['username'],
            'password' => $data['password'],
        ];
    }
}