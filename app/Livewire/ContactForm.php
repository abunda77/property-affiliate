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

    protected $rules = [
        'name' => 'required|string|max:255',
        'whatsapp' => 'required|string|regex:/^[0-9]{10,15}$/',
    ];

    protected $messages = [
        'name.required' => 'Nama wajib diisi.',
        'name.max' => 'Nama maksimal 255 karakter.',
        'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
        'whatsapp.regex' => 'Nomor WhatsApp harus berupa angka 10-15 digit.',
    ];

    public function submit()
    {
        $this->validate();

        // Read affiliate_id from cookie
        $affiliateId = request()->cookie('affiliate_id');

        // Create Lead record
        $lead = Lead::create([
            'affiliate_id' => $affiliateId,
            'property_id' => $this->property->id,
            'name' => $this->name,
            'whatsapp' => $this->whatsapp,
            'status' => LeadStatus::NEW,
        ]);

        // Dispatch LeadCreated event
        event(new LeadCreated($lead));

        // Display success message
        session()->flash('success', 'Terima kasih! Kami akan segera menghubungi Anda.');

        // Reset form
        $this->reset(['name', 'whatsapp']);
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
