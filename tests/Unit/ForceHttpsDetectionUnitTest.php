<?php

namespace Tests\Unit;

use App\Http\Middleware\ForceHttpsDetection;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Tests\TestCase;

class ForceHttpsDetectionUnitTest extends TestCase
{
    protected ForceHttpsDetection $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new ForceHttpsDetection();
    }

    /**
     * Test middleware does nothing when FORCE_HTTPS is disabled.
     */
    public function test_middleware_inactive_when_force_https_disabled(): void
    {
        config(['app.force_https' => false]);
        
        $request = Request::create('http://example.com/test', 'GET');
        
        $response = $this->middleware->handle($request, function ($req) {
            return response('OK');
        });
        
        $this->assertEquals('OK', $response->getContent());
        $this->assertNotInstanceOf(RedirectResponse::class, $response);
    }

    /**
     * Test middleware sets HTTPS server variables with X-Forwarded-Proto header.
     */
    public function test_sets_https_variables_with_x_forwarded_proto(): void
    {
        config(['app.force_https' => true]);
        
        $request = Request::create('http://example.com/test', 'GET');
        $request->headers->set('X-Forwarded-Proto', 'https');
        
        $this->middleware->handle($request, function ($req) {
            $this->assertEquals('on', $req->server->get('HTTPS'));
            $this->assertEquals(443, $req->server->get('SERVER_PORT'));
            $this->assertEquals('https', $req->server->get('REQUEST_SCHEME'));
            return response('OK');
        });
    }

    /**
     * Test middleware sets HTTPS server variables with Cloudflare header.
     */
    public function test_sets_https_variables_with_cloudflare_header(): void
    {
        config(['app.force_https' => true]);
        
        $request = Request::create('http://example.com/test', 'GET');
        $request->headers->set('CF-Visitor', '{"scheme":"https"}');
        
        $this->middleware->handle($request, function ($req) {
            $this->assertEquals('on', $req->server->get('HTTPS'));
            $this->assertEquals(443, $req->server->get('SERVER_PORT'));
            $this->assertEquals('https', $req->server->get('REQUEST_SCHEME'));
            return response('OK');
        });
    }

    /**
     * Test middleware redirects HTTP to HTTPS when forced.
     */
    public function test_redirects_http_to_https_when_forced(): void
    {
        config(['app.force_https' => true]);
        
        $request = Request::create('http://example.com/test', 'GET');
        
        $response = $this->middleware->handle($request, function ($req) {
            return response('OK');
        });
        
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(301, $response->getStatusCode());
        // Check that redirect URL uses HTTPS and contains the path
        $this->assertStringStartsWith('https://', $response->getTargetUrl());
        $this->assertStringContainsString('/test', $response->getTargetUrl());
    }

    /**
     * Test middleware does not redirect when HTTPS is already detected.
     */
    public function test_no_redirect_when_https_detected(): void
    {
        config(['app.force_https' => true]);
        
        $request = Request::create('http://example.com/test', 'GET');
        $request->headers->set('X-Forwarded-Proto', 'https');
        
        $response = $this->middleware->handle($request, function ($req) {
            return response('OK');
        });
        
        $this->assertNotInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('OK', $response->getContent());
    }

    /**
     * Test middleware respects HTTP_X_FORWARDED_PROTO server variable.
     */
    public function test_respects_server_variable(): void
    {
        config(['app.force_https' => true]);
        
        $request = Request::create('http://example.com/test', 'GET', [], [], [], [
            'HTTP_X_FORWARDED_PROTO' => 'https',
        ]);
        
        $this->middleware->handle($request, function ($req) {
            $this->assertEquals('on', $req->server->get('HTTPS'));
            return response('OK');
        });
    }
}
