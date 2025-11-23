<?php

namespace Database\Seeders;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all active affiliates
        $affiliates = User::active()->affiliates()->get();
        
        if ($affiliates->isEmpty()) {
            $this->command->warn('No active affiliates found. Please run UserSeeder first.');
            return;
        }

        // Get all published properties
        $properties = Property::published()->get();
        
        if ($properties->isEmpty()) {
            $this->command->warn('No published properties found. Please run PropertySeeder first.');
            return;
        }

        // Indonesian names for realistic data
        $names = [
            'Andi Wijaya', 'Budi Santoso', 'Citra Dewi', 'Dian Pratama', 'Eka Putri',
            'Fajar Nugroho', 'Gita Savitri', 'Hendra Kusuma', 'Indah Permata', 'Joko Susilo',
            'Kartika Sari', 'Lukman Hakim', 'Maya Anggraini', 'Nanda Pratiwi', 'Oscar Ramadhan',
            'Putri Maharani', 'Qori Sandika', 'Rina Wulandari', 'Sandi Firmansyah', 'Tina Rahayu',
            'Umar Bakri', 'Vina Melati', 'Wawan Setiawan', 'Xena Puspita', 'Yudi Hermawan',
            'Zahra Amelia', 'Agus Salim', 'Bella Safira', 'Candra Wijaya', 'Desi Ratnasari',
            'Eko Prasetyo', 'Fitri Handayani', 'Gilang Ramadhan', 'Hani Purnama', 'Irfan Hakim',
            'Julia Perez', 'Kevin Anggara', 'Lina Marlina', 'Mira Lestari', 'Noval Djokovic',
            'Olivia Zalianty', 'Pandu Winata', 'Qonita Azzahra', 'Reza Rahadian', 'Sinta Nuriyah',
            'Taufik Hidayat', 'Umi Kalsum', 'Vicky Prasetyo', 'Wulan Guritno', 'Yoga Pratama',
        ];

        // Status distribution: 40% new, 25% follow_up, 20% survey, 10% closed, 5% lost
        $statusDistribution = [
            ['status' => LeadStatus::NEW, 'count' => 20],
            ['status' => LeadStatus::FOLLOW_UP, 'count' => 13],
            ['status' => LeadStatus::SURVEY, 'count' => 10],
            ['status' => LeadStatus::CLOSED, 'count' => 5],
            ['status' => LeadStatus::LOST, 'count' => 2],
        ];

        $leadIndex = 0;
        
        foreach ($statusDistribution as $item) {
            $status = $item['status'];
            $count = $item['count'];
            
            for ($i = 0; $i < $count; $i++) {
                // Randomly select affiliate and property
                $affiliate = $affiliates->random();
                $property = $properties->random();
                
                // Generate WhatsApp number
                $whatsapp = '08' . rand(1, 9) . rand(100000000, 999999999);
                
                // Create lead
                $lead = Lead::create([
                    'affiliate_id' => $affiliate->id,
                    'property_id' => $property->id,
                    'name' => $names[$leadIndex],
                    'whatsapp' => $whatsapp,
                    'status' => $status,
                    'notes' => $this->generateNotes($status),
                ]);

                // Set created_at to random date in the last 30 days
                $lead->created_at = now()->subDays(rand(0, 30))->subHours(rand(0, 23));
                $lead->save();

                $leadIndex++;
            }
        }

        $this->command->info('Leads seeded successfully!');
        $this->command->info('Total: 50 leads distributed across ' . $affiliates->count() . ' affiliates');
        $this->command->info('Status distribution: 20 new, 13 follow_up, 10 survey, 5 closed, 2 lost');
    }

    /**
     * Generate realistic notes based on lead status.
     */
    private function generateNotes(LeadStatus $status): ?string
    {
        return match($status) {
            LeadStatus::NEW => null,
            LeadStatus::FOLLOW_UP => 'Sudah dihubungi via WhatsApp. Tertarik untuk survey lokasi minggu depan.',
            LeadStatus::SURVEY => 'Survey sudah dilakukan. Menunggu keputusan dari keluarga. Follow up 3 hari lagi.',
            LeadStatus::CLOSED => 'Deal! Proses pembayaran DP sedang berlangsung. Terima kasih atas kerjasamanya.',
            LeadStatus::LOST => 'Prospek memilih properti lain dengan harga lebih murah. Budget tidak sesuai.',
        };
    }
}
