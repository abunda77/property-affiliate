<?php

namespace App\Filament\Pages\Auth;

use App\Enums\UserStatus;
use App\Models\User;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class Register extends BaseRegister
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getWhatsappFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function getWhatsappFormComponent(): Component
    {
        return TextInput::make('whatsapp')
            ->label('WhatsApp Number')
            ->required()
            ->tel()
            ->maxLength(20);
    }

    protected function handleRegistration(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'whatsapp' => $data['whatsapp'],
            'password' => $data['password'], // Don't hash here, User model will handle it with 'hashed' cast
            'status' => UserStatus::PENDING,
        ]);

        event(new Registered($user));

        return $user;
    }

    protected function afterRegistration(): void
    {
        // Don't auto-login, let user verify email first
        // After email verification, user will be auto-approved
    }
}
