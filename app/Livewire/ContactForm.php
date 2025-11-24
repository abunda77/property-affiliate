<?php

namespace App\Livewire;

use App\Enums\LeadStatus;
use App\Events\LeadCreated;
use App\Models\Lead;
use App\Models\Property;
use Livewire\Component;

class ContactForm extends Component
{
    public Property $property;

    public string $name = '';

    public string $whatsapp = '';

    public string $message = '';

    protected $rules = [
        'name' => [
            'required',
            'string',
            'max:255',
            'regex:/^[\pL\s\-\.]+$/u', // Only letters, spaces, hyphens, and dots
        ],
        'whatsapp' => [
            'required',
            'string',
            'regex:/^[0-9]{10,15}$/',
        ],
        'message' => [
            'nullable',
            'string',
            'max:1000',
        ],
    ];

    protected $messages = [
        'name.required' => 'Nama wajib diisi.',
        'name.max' => 'Nama maksimal 255 karakter.',
        'name.regex' => 'Nama hanya boleh berisi huruf, spasi, tanda hubung, dan titik.',
        'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
        'whatsapp.regex' => 'Nomor WhatsApp harus berupa angka 10-15 digit.',
        'message.max' => 'Pesan maksimal 1000 karakter.',
    ];

    public function submit()
    {
        // Sanitize inputs before validation
        $this->name = strip_tags($this->name);
        $this->whatsapp = preg_replace('/[^0-9]/', '', $this->whatsapp);
        $this->message = strip_tags($this->message);

        $this->validate();

        // Read affiliate_id from cookie
        $affiliateId = request()->cookie('affiliate_id');

        // Create Lead record
        $lead = Lead::create([
            'affiliate_id' => $affiliateId,
            'property_id' => $this->property->id,
            'name' => $this->name,
            'whatsapp' => $this->whatsapp,
            'message' => $this->message,
            'status' => LeadStatus::NEW,
        ]);

        // Dispatch LeadCreated event
        event(new LeadCreated($lead));

        // Display success message
        session()->flash('success', 'Terima kasih! Kami akan segera menghubungi Anda.');

        // Reset form
        $this->reset(['name', 'whatsapp', 'message']);

        // Redirect to show success message
        $this->redirect(request()->header('referer'));
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
