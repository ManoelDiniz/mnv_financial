<?php

namespace App\Services\AI;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class BlackboxAIService
{
    private Client $client;
    private string $apiKey;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.blackbox.ai/v1/',
            'timeout' => 15.0,
        ]);
        $this->apiKey = env('BLACKBOX_API_KEY');
    }

    /**
     * @throws GuzzleException
     */
    public function generateCode(array $parameters): array
    {
        $response = $this->client->post('code/generate', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $parameters
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * @throws GuzzleException
     */
    public function analyzeCode(string $code): array
    {
        $response = $this->client->post('code/analyze', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'code' => $code,
                'language' => 'php'
            ]
        ]);

        return json_decode($response->getBody(), true);
    }
}
