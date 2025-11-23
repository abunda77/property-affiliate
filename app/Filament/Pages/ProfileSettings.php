<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;

class ProfileSettings extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static ?int $navigationSort = 100;

    public ?array $data = [];

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-user-circle';
    }

    public static function getNavigationLabel(): string
    {
        return 'Profil Saya';
    }

    public function getTitle(): string
    {
        return 'Pengaturan Profil';
    }

    public function getView(): string
    {
        return 'filament.pages.profile-settings';
    }

    public function mount(): void
    {
        /** @var User $user */
        $user = Filament::auth()->user();
        
        $this->schema->fill([
            'name' => $user->name,
            'whatsapp' => $user->whatsapp,
            'profile_photo' => $user->profile_photo,
        ]);
    }

    public function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Profil')
                    ->description('Perbarui informasi profil Anda yang akan ditampilkan pada halaman properti.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Masukkan nama lengkap Anda'),

                        TextInput::make('whatsapp')
                            ->label('Nomor WhatsApp')
                            ->required()
                            ->tel()
                            ->regex('/^[0-9]{10,15}$/')
                            ->placeholder('08123456789')
                            ->helperText('Format: 08123456789 (10-15 digit angka)')
                            ->validationMessages([
                                'regex' => 'Nomor WhatsApp harus berupa 10-15 digit angka tanpa spasi atau karakter khusus.',
                            ]),

                        FileUpload::make('profile_photo')
                            ->label('Foto Profil')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                            ])
                            ->maxSize(2048)
                            ->disk('public')
                            ->directory('profile-photos')
                            ->visibility('public')
                            ->helperText('Upload foto profil Anda (maksimal 2MB). Foto akan ditampilkan pada halaman properti yang Anda promosikan.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->schema->getState();
        
        /** @var User $user */
        $user = Filament::auth()->user();
        
        $user->name = $data['name'];
        $user->whatsapp = $data['whatsapp'];
        if (isset($data['profile_photo'])) {
            $user->profile_photo = $data['profile_photo'];
        }
        $user->save();

        Notification::make()
            ->success()
            ->title('Profil berhasil diperbarui')
            ->body('Informasi profil Anda telah berhasil disimpan.')
            ->send();
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User|null $user */
        $user = Filament::auth()->user();
        // Only show for affiliates (users with affiliate_code)
        return $user !== null && $user->affiliate_code !== null;
    }
}
