<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use App\Services\GoWAService;
use Filament\Facades\Filament;
use Filament\Pages\SettingsPage;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Actions\Action;

class Settings extends SettingsPage
{
    protected static string $settings = GeneralSettings::class;

    protected static ?string $navigationLabel = 'Pengaturan Sistem';

    protected static ?string $title = 'Pengaturan Sistem';

    protected static ?int $navigationSort = 100;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public static function canAccess(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Filament::auth()->user();
        return $user?->roles()->where('name', 'super_admin')->exists() ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('testGowaConnection')
                ->label('Test Koneksi GoWA')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Test Koneksi GoWA API')
                ->modalDescription('Pesan test akan dikirim ke nomor yang Anda masukkan di field "Nomor Test".')
                ->modalSubmitActionLabel('Kirim Test')
                ->action(function () {
                    $data = $this->form->getState();
                    
                    $username = $data['gowa_username'] ?? null;
                    $password = $data['gowa_password'] ?? null;
                    $apiUrl = $data['gowa_api_url'] ?? null;
                    $testPhone = $data['test_phone'] ?? null;

                    if (empty($testPhone)) {
                        Notification::make()
                            ->title('Nomor test tidak boleh kosong')
                            ->body('Silakan isi nomor test terlebih dahulu.')
                            ->danger()
                            ->send();
                        return;
                    }

                    if (empty($username) || empty($password) || empty($apiUrl)) {
                        Notification::make()
                            ->title('Kredensial tidak lengkap')
                            ->body('Pastikan username, password, dan URL API sudah diisi.')
                            ->warning()
                            ->send();
                        return;
                    }

                    // Create service instance with test credentials
                    $gowaService = new GoWAService($username, $password, $apiUrl);
                    $result = $gowaService->testConnection($testPhone);

                    if ($result['success']) {
                        Notification::make()
                            ->title('Test Berhasil!')
                            ->body($result['message'])
                            ->success()
                            ->duration(5000)
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Test Gagal')
                            ->body($result['message'])
                            ->danger()
                            ->duration(8000)
                            ->send();
                    }
                }),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Konfigurasi GoWA API')
                    ->description('Pengaturan untuk integrasi WhatsApp melalui GoWA API')
                    ->schema([
                        TextInput::make('gowa_username')
                            ->label('GoWA Username')
                            ->helperText('Username untuk autentikasi GoWA API')
                            ->maxLength(255),
                        TextInput::make('gowa_password')
                            ->label('GoWA Password')
                            ->password()
                            ->revealable()
                            ->helperText('Password untuk autentikasi GoWA API')
                            ->maxLength(255),
                        TextInput::make('gowa_api_url')
                            ->label('GoWA API URL')
                            ->helperText('URL endpoint API GoWA (contoh: http://localhost:3000)')
                            ->default('http://localhost:3000')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('test_phone')
                            ->label('Nomor Test')
                            ->tel()
                            ->helperText('Nomor WhatsApp untuk test koneksi. Klik tombol "Test Koneksi GoWA" di atas untuk mengirim pesan test.')
                            ->placeholder('089685028129')
                            ->maxLength(20)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Logo & Branding')
                    ->description('Upload logo untuk ditampilkan di header dan footer website')
                    ->schema([
                        FileUpload::make('logo_path')
                            ->label('Logo Website')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->directory('logos')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->helperText('Upload logo dalam format PNG, JPG, atau SVG (maksimal 2MB)'),
                    ]),

                Section::make('Pengaturan SEO')
                    ->description('Konfigurasi meta tags untuk optimasi mesin pencari')
                    ->schema([
                        TextInput::make('seo_meta_title')
                            ->label('Meta Title')
                            ->helperText('Judul yang akan muncul di hasil pencarian Google')
                            ->maxLength(60)
                            ->required(),
                        Textarea::make('seo_meta_description')
                            ->label('Meta Description')
                            ->helperText('Deskripsi singkat website (maksimal 160 karakter)')
                            ->maxLength(160)
                            ->rows(3)
                            ->required(),
                        Textarea::make('seo_meta_keywords')
                            ->label('Meta Keywords')
                            ->helperText('Kata kunci dipisahkan dengan koma')
                            ->rows(2),
                    ]),

                Section::make('Informasi Kontak')
                    ->description('Informasi kontak yang ditampilkan di footer website')
                    ->schema([
                        TextInput::make('contact_email')
                            ->label('Email Kontak')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('contact_whatsapp')
                            ->label('WhatsApp Kontak')
                            ->tel()
                            ->helperText('Format: +62 xxx xxxx xxxx')
                            ->maxLength(20),
                    ])
                    ->columns(2),
            ]);
    }
}
