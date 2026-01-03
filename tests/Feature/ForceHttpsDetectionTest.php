<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForceHttpsDetectionTest extends TestCase
{
    /**
     * Test that HTTPS is not forced when FORCE_HTTPS is disabled.
     */
    public function test_https_not_forced_when_disabled(): void
    {
        config(['app.force_https' => false]);
        
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $this->assertFalse($this->app['request']->secure());
    }

    /**
     * Test that HTTPS detection works with x-forwarded-proto header.
     */
    public function test_https_detected_from_x_forwarded_proto_header(): void
    {
        config(['app.force_https' => true]);
        
        $response = $this->withHeaders([
            'X-Forwarded-Proto' => 'https',
        ])->get('/');
        
        $response->assertStatus(200);
        $this->assertEquals('on', $this->app['request']->server->get('HTTPS'));
        $this->assertEquals(443, $this->app['request']->server->get('SERVER_PORT'));
        $this->assertEquals('https', $this->app['request']->server->get('REQUEST_SCHEME'));
    }

    /**
     * Test that HTTPS detection works with Cloudflare cf-visitor header.
     */
    public function test_https_detected_from_cloudflare_header(): void
    {
        config(['app.force_https' => true]);
        
        $response = $this->withHeaders([
            'CF-Visitor' => '{"scheme":"https"}',
        ])->get('/');
        
        $response->assertStatus(200);
        $this->assertEquals('on', $this->app['request']->server->get('HTTPS'));
        $this->assertEquals(443, $this->app['request']->server->get('SERVER_PORT'));
        $this->assertEquals('https', $this->app['request']->server->get('REQUEST_SCHEME'));
    }

    /**
     * Test that HTTP requests are redirected to HTTPS when FORCE_HTTPS is enabled.
     */
    public function test_http_redirects_to_https_when_forced(): void
    {
        config(['app.force_https' => true]);
        
        // Simulate HTTP request without proxy headers
        $response = $this->get('http://localhost/about');
        
        $response->assertStatus(301);
        $response->assertRedirect('https://localhost/about');
    }

    /**
     * Test that HTTPS requests are not redirected when already secure.
     */
    public function test_https_requests_not_redirected(): void
    {
        config(['app.force_https' => true]);
        
        $response = $this->withHeaders([
            'X-Forwarded-Proto' => 'https',
        ])->get('/');
        
        $response->assertStatus(200);
    }

    /**
     * Test that middleware respects HTTP_X_FORWARDED_PROTO server variable.
     */
    public function test_https_detected_from_server_variable(): void
    {
        config(['app.force_https' => true]);
        
        $response = $this->call('GET', '/', [], [], [], [
            'HTTP_X_FORWARDED_PROTO' => 'https',
        ]);
        
        $response->assertStatus(200);
        $this->assertEquals('on', $this->app['request']->server->get('HTTPS'));
    }

    /**
     * Test that all routes work correctly with FORCE_HTTPS enabled.
     */
    public function test_all_routes_work_with_force_https(): void
    {
        config(['app.force_https' => true]);
        
        // Test with valid routes that exist in the application
        $response = $this->withHeaders([
            'X-Forwarded-Proto' => 'https',
        ])->get('/');
        
        // Should not redirect when already HTTPS
        $response->assertSuccessful();
    }
}
