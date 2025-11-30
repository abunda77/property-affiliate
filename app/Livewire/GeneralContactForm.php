<?php

namespace App\Livewire;

use App\Settings\GeneralSettings;
use Livewire\Component;

class GeneralContactForm extends Component
{
    public string $name = '';
    public string $email = '';
    public string $whatsapp = '';
    public string $subject = '';
    public string $message = '';

    protected $rules = [
        'name' => [
            'required',
            'string',
            'max:255',
            'regex:/^[\pL\s\-\.]+$/u',
        ],
        'email' => [
            'required',
            'email',
            'max:255',
        ],
        'whatsapp' => [
            'required',
            'string',
            'regex:/^[0-9]{10,15}$/',
        ],
        'subject' => [
            'required',
            'string',
            'max:255',
        ],
        'message' => [
            'required',
            'string',
            'max:2000',
        ],
    ];

    protected $messages = [
        'name.required' => 'Nama wajib diisi.',
        'name.max' => 'Nama maksimal 255 karakter.',
        'name.regex' => 'Nama hanya boleh berisi huruf, spasi, tanda hubung, dan titik.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
        'whatsapp.regex' => 'Nomor WhatsApp harus berupa angka 10-15 digit.',
        'subject.required' => 'Subjek wajib diisi.',
        'message.required' => 'Pesan wajib diisi.',
        'message.max' => 'Pesan maksimal 2000 karakter.',
    ];

    public function submit()
    {
        // Sanitize inputs
        $this->name = strip_tags($this->name);
        $this->email = strip_tags($this->email);
        $this->whatsapp = preg_replace('/[^0-9]/', '', $this->whatsapp);
        $this->subject = strip_tags($this->subject);
        $this->message = strip_tags($this->message);

        $this->validate();

        try {
            $settings = app(GeneralSettings::class);
        } catch (\Spatie\LaravelSettings\Exceptions\MissingSettings $e) {
            $settings = null;
        }

        // Send email if configured
        if ($settings && $settings->contact_email) {
            try {
                \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($settings) {
                    $message->to($settings->contact_email)
                        ->subject("Kontak: {$this->subject}")
                        ->html("
                            <h2>Pesan Kontak Baru</h2>
                            <p><strong>Nama:</strong> {$this->name}</p>
                            <p><strong>Email:</strong> {$this->email}</p>
                            <p><strong>WhatsApp:</strong> {$this->whatsapp}</p>
                            <p><strong>Subjek:</strong> {$this->subject}</p>
                            <p><strong>Pesan:</strong></p>
                            <p>{$this->message}</p>
                        ");
                });
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send contact email: ' . $e->getMessage());
            }
        }

        session()->flash('success', 'Terima kasih! Pesan Anda telah terkirim. Kami akan segera menghubungi Anda.');

        // Reset form
        $this->reset(['name', 'email', 'whatsapp', 'subject', 'message']);
    }

    public function render()
    {
        try {
            $settings = app(GeneralSettings::class);
            $contactEmail = $settings->contact_email;
            $contactWhatsapp = $settings->contact_whatsapp;
        } catch (\Spatie\LaravelSettings\Exceptions\MissingSettings $e) {
            $contactEmail = null;
            $contactWhatsapp = null;
        }
        
        return view('livewire.general-contact-form', [
            'contactEmail' => $contactEmail,
            'contactWhatsapp' => $contactWhatsapp,
        ]);
    }
}
