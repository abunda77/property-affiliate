<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Seeder;

class VisitSeeder extends Seeder
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

        // Device types distribution: 60% mobile, 40% desktop
        $devices = [
            'mobile' => 300,
            'desktop' => 200,
        ];

        // Browser distribution
        $browsers = [
            'Chrome' => 250,
            'Safari' => 120,
            'Firefox' => 70,
            'Edge' => 40,
            'Opera' => 20,
        ];

        // Generate realistic IP addresses
        $ipPrefixes = [
            '103.', '114.', '125.', '180.', '202.',
            '36.', '110.', '139.', '182.', '203.',
        ];

        $visitCount = 0;
        $browserList = [];
        
        // Prepare browser list based on distribution
        foreach ($browsers as $browser => $count) {
            for ($i = 0; $i < $count; $i++) {
                $browserList[] = $browser;
            }
        }

        // Create visits for each device type
        foreach ($devices as $device => $count) {
            for ($i = 0; $i < $count; $i++) {
                // Randomly select affiliate and property
                $affiliate = $affiliates->random();
                $property = $properties->random();
                
                // Generate random IP address
                $ipPrefix = $ipPrefixes[array_rand($ipPrefixes)];
                $visitorIp = $ipPrefix . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255);
                
                // Get browser from list
                $browser = $browserList[$visitCount];
                
                // Generate URL
                $url = url('/p/' . $property->slug . '?ref=' . $affiliate->affiliate_code);
                
                // Create visit
                $visit = Visit::create([
                    'affiliate_id' => $affiliate->id,
                    'property_id' => $property->id,
                    'visitor_ip' => $visitorIp,
                    'device' => $device,
                    'browser' => $browser,
                    'url' => $url,
                ]);

                // Set created_at to random date/time in the last 60 days
                // More recent visits should be more common
                $daysAgo = $this->getWeightedRandomDays();
                $visit->created_at = now()
                    ->subDays($daysAgo)
                    ->subHours(rand(0, 23))
                    ->subMinutes(rand(0, 59));
                $visit->save();

                $visitCount++;
            }
        }

        $this->command->info('Visits seeded successfully!');
        $this->command->info('Total: 500 visits distributed across ' . $affiliates->count() . ' affiliates');
        $this->command->info('Device distribution: 300 mobile (60%), 200 desktop (40%)');
        $this->command->info('Browser distribution: Chrome (250), Safari (120), Firefox (70), Edge (40), Opera (20)');
    }

    /**
     * Get weighted random days (more recent visits are more common).
     * 
     * @return int
     */
    private function getWeightedRandomDays(): int
    {
        $rand = rand(1, 100);
        
        // 40% of visits in last 7 days
        if ($rand <= 40) {
            return rand(0, 7);
        }
        
        // 30% of visits in last 8-21 days
        if ($rand <= 70) {
            return rand(8, 21);
        }
        
        // 20% of visits in last 22-45 days
        if ($rand <= 90) {
            return rand(22, 45);
        }
        
        // 10% of visits in last 46-60 days
        return rand(46, 60);
    }
}
