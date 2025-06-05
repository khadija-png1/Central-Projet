<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class UrlCheckerService
{
    public function __construct(private readonly HttpClientInterface $client) {}

    public function checkUrl(string $url): array
    {
        if (!str_starts_with($url, 'http')) {
            $url = 'http://' . $url;
        }

        try {
            $response = $this->client->request('GET', $url, ['timeout' => 5]);
            $statusCode = $response->getStatusCode();

            return [
                'success' => $statusCode < 400,
                'status_code' => $statusCode,
                'url' => $url,
                'error' => null
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'status_code' => null,
                'url' => $url,
                'error' => $e->getMessage()
            ];
        }
        
    }
}
