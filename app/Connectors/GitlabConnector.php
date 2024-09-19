<?php

namespace App\Connectors;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Log;

use Exception;


class GitlabConnector implements ConnectorInterface
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function search(string $query): array
    {
        try {
            $resp = $this->client->get('https://gitlab.com/api/v4/projects?search=' . $query . '&per_page=5&order_by=id&sort=asc');
        } catch (ServerException $e) {
            // Log the exception message + stack trace
            Log::error('Unable to contact API server: ' . $e->getMessage(), ['exception' => $e]);
            
            // Return an empty array in case of a server error, to keep the app running smoothly
            return [];
        }

        $repositories = @json_decode($resp->getBody()->getContents(), true);
        if ($repositories == null) {
            if ($resp->getStatusCode() == 400) {
                // Log the error message in case the error was due to a Bad Request
                Log::error('Unable to parse JSON response');
            }

            // Return an empty array if no repositories were found (not an error)
            return [];
        }

        $return = [];
        foreach ($repositories as $repository) {
            $return[] = [
                'repository' => $repository['name'],
                'full_repository_name' => $repository['path_with_namespace'],
                'description' => $repository['description'],
                'creator' => @$repository['namespace']['path'] ?: $repository['namespace']['id'],
            ];
        }

        return $return;
    }
}