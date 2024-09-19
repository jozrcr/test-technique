<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

use Exception;

class RepositoryService
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function search(string $query): array
    {
        try {
            $resp = $this->client->get('https://api.github.com/search/repositories?per_page=5&q=' . $query);
        } catch (GuzzleException $e) {
            throw new Exception("Unable to contact API server.");
        }

        $repositories = @json_decode($resp->getBody()->getContents(), true)['items'];
        if ($repositories === null) {
            throw new Exception("Unable to parse JSON resp.");
        }

        $return = [];
        foreach ($repositories as $r) {
            $return[] = [
                'name' => $r['name'],
                'full_name' => $r['full_name'],
                'description' => $r['description'],
                'owner' => $r['owner']['username'] ?? $r['owner']['login'],
            ];
        }

        return $return;
    }
}
