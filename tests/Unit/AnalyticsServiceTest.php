<?php

namespace Tests\Unit;

use App\Models\Lead;
use App\Models\Property;
use App\Models\User;
use App\Models\Visit;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AnalyticsService $analyticsService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->analyticsService = new AnalyticsService();
    }

    public function test_get_affiliate_metrics_returns_correct_structure(): void
    {
        $user = User::factory()->create([
            'affiliate_code' => 'TEST1234',
        ]);

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now();

        $metrics = $this->analyticsService->getAffiliateMetrics($user, $startDate, $endDate);

        $this->assertIsArray($metrics);
        $this->assertArrayHasKey('total_visits', $metrics);
        $this->assertArrayHasKey('total_leads', $metrics);
        $this->assertArrayHasKey('conversion_rate', $metrics);
        $this->assertArrayHasKey('device_breakdown', $metrics);
        $this->assertArrayHasKey('top_properties', $metrics);
    }

    public function test_calculates_conversion_rate_correctly(): void
    {
        $user = User::factory()->create([
            'affiliate_code' => 'TEST1234',
        ]);

        $property = Property::factory()->create();

        // Create 10 visits
        Visit::factory()->count(10)->create([
            'affiliate_id' => $user->id,
            'property_id' => $property->id,
            'created_at' => Carbon::now(),
        ]);

        // Create 2 leads (20% conversion rate)
        Lead::factory()->count(2)->create([
            'affiliate_id' => $user->id,
            'property_id' => $property->id,
            'created_at' => Carbon::now(),
        ]);

        $startDate = Carbon::today();
        $endDate = Carbon::now();

        $metrics = $this->analyticsService->getAffiliateMetrics($user, $startDate, $endDate);

        $this->assertEquals(10, $metrics['total_visits']);
        $this->assertEquals(2, $metrics['total_leads']);
        $this->assertEquals(20.0, $metrics['conversion_rate']);
    }

    public function test_device_breakdown_groups_correctly(): void
    {
        $user = User::factory()->create([
            'affiliate_code' => 'TEST1234',
        ]);

        Visit::factory()->count(3)->create([
            'affiliate_id' => $user->id,
            'device' => 'mobile',
            'created_at' => Carbon::now(),
        ]);

        Visit::factory()->count(2)->create([
            'affiliate_id' => $user->id,
            'device' => 'desktop',
            'created_at' => Carbon::now(),
        ]);

        $startDate = Carbon::today();
        $endDate = Carbon::now();

        $metrics = $this->analyticsService->getAffiliateMetrics($user, $startDate, $endDate);

        $this->assertEquals(3, $metrics['device_breakdown']['mobile']);
        $this->assertEquals(2, $metrics['device_breakdown']['desktop']);
    }
}
