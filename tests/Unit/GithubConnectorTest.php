<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Connectors\GithubConnector;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

use Tests\TestCase;

class GithubConnectorTest extends TestCase
{
    public function test_search_returns_empty_array_when_unable_to_contact_api_server(): void
    {
        // Create a mock HTTP client
        $client = $this->createMock(Client::class);

        // Configure the mock client to throw a ServerException
        $client->method('get')->willThrowException(new ServerException('Unable to contact API server', new Request('GET', 'https://api.github.com/search/repositories?per_page=5&q=query'), new Response(500, [])));

        // Create a GithubConnector instance with the mock client
        $githubConnector = new GithubConnector($client);

        // Call the search method with a query string which matches the mock client
        $repositories = $githubConnector->search('query');

        // Assert that the search method returns an empty array
        $this->assertEmpty($repositories);
    }

    public function test_search_returns_empty_array_when_no_repositories_are_found(): void
    {
        $client = new Client;

        // Create a GithubConnector instance 
        $githubConnector = new GithubConnector($client);

        // Call the search method with a query string that wont find any repo 
        $repositories = $githubConnector->search('hzfvhgkwytoa'); // Random string that has no match

        // Assert that the search method returns an empty array
        $this->assertEmpty($repositories);
    }

    public function test_search_returns_right_repositories_data(): void
    {
        $client = new Client;

        // Create a GithubConnector instance 
        $githubConnector = new GithubConnector($client);

        // Call the search method with a query string
        $repositories = $githubConnector->search('courses');

        // Fetch expected repositories' data stored in a dedicated json
        $expected = $this->getExpected(__DIR__.'/../Expected/Repository/courses_github_search.json');

        foreach ($expected as $repository) {
            $this->assertContains($repository, $repositories);
        }
    }

}
