<?php

declare(strict_types=1);

namespace App\Services;

use App\Connectors\GithubConnector;
use App\Connectors\GitlabConnector;

use Exception;

class RepositoryService
{
    private $githubConnector;
    private $gitlabConnector;

    public function __construct(GithubConnector $githubConnector, GitlabConnector $gitlabConnector)
    {
        $this->githubConnector = $githubConnector;
        $this->gitlabConnector = $gitlabConnector;
    }

    public function search(string $query, string $provider): array
    {

        $repositories = [];

        if ($provider === 'github'){

            $repositories = $this->githubConnector->search($query);

        }

        elseif ($provider === 'gitlab'){

            $repositories = $this->gitlabConnector->search($query);

        }

        else {
            
                throw new Exception("Invalid provider: $provider");
        }

        return $repositories;
    }
}
