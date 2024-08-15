<?php

namespace OpenSoutheners\ExtendedLaravel\Http;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

abstract class GeneratedClient
{
    protected PendingRequest $client;

    public function __construct()
    {
        $this->client = $this->setUp();
    }

    public function setUp(): PendingRequest
    {
        return $this->asFake()
            ? Http::fake()->baseUrl('https://getskore.com')
            : Http::baseUrl(config('services.netcall_one.base_url', ''));
    }

    public function asFake(): bool
    {
        return empty(config('services.netcall_one.base_url', ''));
    }

    public function __call(string $method, array $arguments)
    {
        $function = Str::snake($method);

        if (! $functionMethod = self::FUNCTION_MAP[$function] ?? null) {
            throw new \Exception('Method not yet implemented in the client.');
        }

        $response = $this->client->asJson()->send($functionMethod, '/', [
            'json' => [
                'client_unique_identifier' => (string) Str::ulid(),
                'function' => Str::snake($method),
                'data' => $arguments,
            ],
        ]);

        return $response;
    }
}
