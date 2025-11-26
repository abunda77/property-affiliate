<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Notifications\Notification;

class Login extends BaseLogin
{
    public function mount(): void
    {
        parent::mount();

        // Check for blocked user message from session
        if (session()->has('error')) {
            Notification::make()
                ->danger()
                ->title('Account Blocked')
                ->body(session('error'))
                ->persistent()
                ->send();
        }
    }
}
