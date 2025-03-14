<?php

namespace OpenSoutheners\ExtendedLaravel\Tests\Http\Middleware;

use Illuminate\Support\Facades\Route;
use OpenSoutheners\ExtendedLaravel\Http\Middleware\ForceHttpsScheme;
use OpenSoutheners\ExtendedLaravel\Tests\TestCase;

class ForceHttpsSchemeTest extends TestCase
{
    public function testWithoutForceHttpsSchemeMiddlewareUsedOnRouteReturnsAllUrlsAsHttp()
    {
        Route::get('/test', fn () => response()->json(['path' => asset('/')]));

        $response = $this->getJson(url('/test'));

        $response->assertOk();

        $parsedResponseUrl = parse_url($response->json('path'));

        $this->assertEquals('http', $parsedResponseUrl['scheme']);
    }

    public function testForceHttpsSchemeMiddlewareUsedOnRouteReturnsAllUrlsAsHttps()
    {
        Route::get('/test', fn () => response()->json(['path' => url('/')]))
            ->middleware(ForceHttpsScheme::class);

        $response = $this->getJson(url('/test'));

        $response->assertOk();

        $parsedResponseUrl = parse_url($response->json('path'));

        $this->assertEquals('https', $parsedResponseUrl['scheme']);
    }
}
