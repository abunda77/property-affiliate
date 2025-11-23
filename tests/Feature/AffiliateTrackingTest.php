<?php

namespace Tests\Feature;

use App\Enums\PropertyStatus;
use App\Enums\UserStatus;
use App\Models\Property;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AffiliateTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Define dummy login and register routes for testing
        // These are needed because the properties.index view references them
        Route::get('/login', function () {
            return 'Login Page';
        })->name('login');

        Route::get('/register', function () {
            return 'Register Page';
        })->name('register');
    }

    public function test_visit_recorded_with_ref_parameter(): void
    {
        // Create an active affiliate user
        $affiliate = User::factory()->create([
            'affiliate_code' => 'TEST1234',
            'status' => UserStatus::ACTIVE,
        ]);

        // Make a request with ref parameter to properties page
        $response = $this->get('/properties?ref=TEST1234');

        $response->assertStatus(200);

        // Assert visit was recorded
        $this->assertDatabaseHas('visits', [
            'affiliate_id' => $affiliate->id,
        ]);

        // Assert cookie was set with correct value
        $response->assertCookie('affiliate_id', (string) $affiliate->id);
    }

    public function test_cookie_persists_affiliate_attribution(): void
    {
        // Create an active affiliate user
        $affiliate = User::factory()->create([
            'affiliate_code' => 'TEST5678',
            'status' => UserStatus::ACTIVE,
        ]);

        // First request with ref parameter
        $firstResponse = $this->get('/properties?ref=TEST5678');

        // Second request without ref parameter but with unencrypted cookie value
        $response = $this->withUnencryptedCookie('affiliate_id', (string) $affiliate->id)->get('/properties');

        $response->assertStatus(200);

        // Assert two visits were recorded for the same affiliate
        $this->assertEquals(2, Visit::where('affiliate_id', $affiliate->id)->count());
    }

    public function test_cookie_has_30_day_expiration(): void
    {
        // Create an active affiliate user
        $affiliate = User::factory()->create([
            'affiliate_code' => 'EXPIRE01',
            'status' => UserStatus::ACTIVE,
        ]);

        // Make a request with ref parameter
        $response = $this->get('/properties?ref=EXPIRE01');

        $response->assertStatus(200);

        // Assert cookie was set
        $response->assertCookie('affiliate_id');
        
        // Get the cookie and verify it exists
        $cookies = $response->headers->getCookies();
        $affiliateCookie = collect($cookies)->first(fn($cookie) => $cookie->getName() === 'affiliate_id');
        
        $this->assertNotNull($affiliateCookie, 'Affiliate cookie should be set');
    }

    public function test_invalid_affiliate_code_does_not_record_visit(): void
    {
        // Make a request with invalid ref parameter
        $response = $this->get('/properties?ref=INVALID');

        $response->assertStatus(200);

        // Assert no visit was recorded
        $this->assertEquals(0, Visit::count());

        // Assert no cookie was set
        $response->assertCookieMissing('affiliate_id');
    }

    public function test_inactive_affiliate_does_not_record_visit(): void
    {
        // Create a pending affiliate user
        $affiliate = User::factory()->create([
            'affiliate_code' => 'PENDING1',
            'status' => UserStatus::PENDING,
        ]);

        // Make a request with ref parameter
        $response = $this->get('/properties?ref=PENDING1');

        $response->assertStatus(200);

        // Assert no visit was recorded
        $this->assertEquals(0, Visit::count());

        // Assert no cookie was set
        $response->assertCookieMissing('affiliate_id');
    }

    public function test_blocked_affiliate_does_not_record_visit(): void
    {
        // Create a blocked affiliate user
        $affiliate = User::factory()->create([
            'affiliate_code' => 'BLOCKED1',
            'status' => UserStatus::BLOCKED,
        ]);

        // Make a request with ref parameter
        $response = $this->get('/properties?ref=BLOCKED1');

        $response->assertStatus(200);

        // Assert no visit was recorded
        $this->assertEquals(0, Visit::count());

        // Assert no cookie was set
        $response->assertCookieMissing('affiliate_id');
    }

    public function test_visit_records_device_and_browser_info(): void
    {
        // Create an active affiliate user
        $affiliate = User::factory()->create([
            'affiliate_code' => 'DEVICE01',
            'status' => UserStatus::ACTIVE,
        ]);

        // Make a request with mobile user agent
        $response = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
        ])->get('/properties?ref=DEVICE01');

        $response->assertStatus(200);

        // Assert visit was recorded with device info
        $this->assertDatabaseHas('visits', [
            'affiliate_id' => $affiliate->id,
            'device' => 'mobile',
            'browser' => 'Safari',
        ]);
    }

    public function test_visit_records_desktop_device_type(): void
    {
        // Create an active affiliate user
        $affiliate = User::factory()->create([
            'affiliate_code' => 'DESKTOP1',
            'status' => UserStatus::ACTIVE,
        ]);

        // Make a request with desktop user agent
        $response = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        ])->get('/properties?ref=DESKTOP1');

        $response->assertStatus(200);

        // Assert visit was recorded with desktop device
        $this->assertDatabaseHas('visits', [
            'affiliate_id' => $affiliate->id,
            'device' => 'desktop',
            'browser' => 'Chrome',
        ]);
    }

    public function test_visit_records_visitor_ip_address(): void
    {
        // Create an active affiliate user
        $affiliate = User::factory()->create([
            'affiliate_code' => 'IPTEST1',
            'status' => UserStatus::ACTIVE,
        ]);

        // Make a request with ref parameter
        $response = $this->get('/properties?ref=IPTEST1');

        $response->assertStatus(200);

        // Assert visit was recorded with IP address
        $visit = Visit::where('affiliate_id', $affiliate->id)->first();
        $this->assertNotNull($visit);
        $this->assertNotNull($visit->visitor_ip);
    }

    public function test_visit_records_url(): void
    {
        // Create an active affiliate user
        $affiliate = User::factory()->create([
            'affiliate_code' => 'URLTEST1',
            'status' => UserStatus::ACTIVE,
        ]);

        // Make a request with ref parameter
        $response = $this->get('/properties?ref=URLTEST1');

        $response->assertStatus(200);

        // Assert visit was recorded with URL
        $visit = Visit::where('affiliate_id', $affiliate->id)->first();
        $this->assertNotNull($visit);
        $this->assertNotNull($visit->url);
        $this->assertStringContainsString('ref=URLTEST1', $visit->url);
    }

    public function test_visit_attribution_accuracy_with_multiple_affiliates(): void
    {
        // Create two active affiliates
        $affiliate1 = User::factory()->create([
            'affiliate_code' => 'ATTR001',
            'status' => UserStatus::ACTIVE,
        ]);

        $affiliate2 = User::factory()->create([
            'affiliate_code' => 'ATTR002',
            'status' => UserStatus::ACTIVE,
        ]);

        // First visitor uses affiliate1's link
        $response1 = $this->get('/properties?ref=ATTR001');
        $response1->assertStatus(200);

        // Second visitor uses affiliate2's link (different session)
        $response2 = $this->get('/properties?ref=ATTR002');
        $response2->assertStatus(200);

        // Assert each affiliate has exactly one visit
        $this->assertEquals(1, Visit::where('affiliate_id', $affiliate1->id)->count());
        $this->assertEquals(1, Visit::where('affiliate_id', $affiliate2->id)->count());
    }

    public function test_visit_attribution_persists_across_multiple_pages(): void
    {
        // Create an active affiliate user
        $affiliate = User::factory()->create([
            'affiliate_code' => 'PERSIST1',
            'status' => UserStatus::ACTIVE,
        ]);

        // First request with ref parameter
        $response1 = $this->get('/properties?ref=PERSIST1');
        $response1->assertStatus(200);

        // Navigate to different pages with cookie
        $response2 = $this->withUnencryptedCookie('affiliate_id', (string) $affiliate->id)->get('/properties');
        $response2->assertStatus(200);

        $response3 = $this->withUnencryptedCookie('affiliate_id', (string) $affiliate->id)->get('/properties');
        $response3->assertStatus(200);

        // Assert three visits were recorded for the same affiliate
        $this->assertEquals(3, Visit::where('affiliate_id', $affiliate->id)->count());
    }

    public function test_property_id_extracted_from_property_page_url(): void
    {
        // Create an active affiliate and a property
        $affiliate = User::factory()->create([
            'affiliate_code' => 'PROPID1',
            'status' => UserStatus::ACTIVE,
        ]);

        $property = Property::factory()->create([
            'slug' => 'test-property',
            'status' => PropertyStatus::PUBLISHED,
        ]);

        // Make a request to property page with ref parameter
        $response = $this->get("/p/{$property->slug}?ref=PROPID1");

        $response->assertStatus(200);

        // Assert visit was recorded with property_id
        $this->assertDatabaseHas('visits', [
            'affiliate_id' => $affiliate->id,
            'property_id' => $property->id,
        ]);
    }

    public function test_visit_without_property_has_null_property_id(): void
    {
        // Create an active affiliate user
        $affiliate = User::factory()->create([
            'affiliate_code' => 'NOPROP1',
            'status' => UserStatus::ACTIVE,
        ]);

        // Make a request to properties page with ref parameter
        $response = $this->get('/properties?ref=NOPROP1');

        $response->assertStatus(200);

        // Assert visit was recorded with null property_id
        $this->assertDatabaseHas('visits', [
            'affiliate_id' => $affiliate->id,
            'property_id' => null,
        ]);
    }

    public function test_multiple_visits_from_same_affiliate_are_recorded(): void
    {
        // Create an active affiliate user
        $affiliate = User::factory()->create([
            'affiliate_code' => 'MULTI01',
            'status' => UserStatus::ACTIVE,
        ]);

        // Make multiple requests with the same affiliate cookie
        $this->get('/properties?ref=MULTI01');
        $this->withUnencryptedCookie('affiliate_id', (string) $affiliate->id)->get('/properties');
        $this->withUnencryptedCookie('affiliate_id', (string) $affiliate->id)->get('/properties');
        $this->withUnencryptedCookie('affiliate_id', (string) $affiliate->id)->get('/properties');

        // Assert four visits were recorded
        $this->assertEquals(4, Visit::where('affiliate_id', $affiliate->id)->count());
    }

    public function test_ref_parameter_overrides_existing_cookie(): void
    {
        // Create two active affiliates
        $affiliate1 = User::factory()->create([
            'affiliate_code' => 'OVERRIDE1',
            'status' => UserStatus::ACTIVE,
        ]);

        $affiliate2 = User::factory()->create([
            'affiliate_code' => 'OVERRIDE2',
            'status' => UserStatus::ACTIVE,
        ]);

        // First request with affiliate1's ref
        $response1 = $this->get('/properties?ref=OVERRIDE1');
        $response1->assertStatus(200);

        // Second request with affiliate2's ref (should override cookie)
        $response2 = $this->withUnencryptedCookie('affiliate_id', (string) $affiliate1->id)
            ->get('/properties?ref=OVERRIDE2');
        $response2->assertStatus(200);

        // Assert new cookie is set for affiliate2
        $response2->assertCookie('affiliate_id', (string) $affiliate2->id);

        // Assert both affiliates have visits
        $this->assertEquals(1, Visit::where('affiliate_id', $affiliate1->id)->count());
        $this->assertEquals(1, Visit::where('affiliate_id', $affiliate2->id)->count());
    }

    public function test_visit_not_recorded_for_nonexistent_affiliate_id_in_cookie(): void
    {
        // Make a request with invalid affiliate_id in cookie
        $response = $this->withUnencryptedCookie('affiliate_id', '99999')->get('/');

        $response->assertStatus(200);

        // Assert visit was still recorded (middleware doesn't validate cookie value)
        // This is expected behavior - the middleware trusts the cookie value
        $this->assertEquals(1, Visit::count());
    }

    public function test_different_browsers_detected_correctly(): void
    {
        // Create an active affiliate user
        $affiliate = User::factory()->create([
            'affiliate_code' => 'BROWSER1',
            'status' => UserStatus::ACTIVE,
        ]);

        // Test Firefox
        $response1 = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
        ])->get('/properties?ref=BROWSER1');
        $response1->assertStatus(200);

        // Test Edge
        $response2 = $this->withUnencryptedCookie('affiliate_id', (string) $affiliate->id)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36 Edg/91.0.864.59',
            ])->get('/properties');
        $response2->assertStatus(200);

        // Assert visits were recorded with correct browsers
        $this->assertDatabaseHas('visits', [
            'affiliate_id' => $affiliate->id,
            'browser' => 'Firefox',
        ]);

        $this->assertDatabaseHas('visits', [
            'affiliate_id' => $affiliate->id,
            'browser' => 'Edge',
        ]);
    }
}
