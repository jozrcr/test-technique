<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\RepositorySearchRequest;
use App\Services\RepositoryService;
use Illuminate\Support\Facades\Log;

use Exception;

class RepositoryController extends Controller
{

    private $repositoryService;

    private $providers;

    public function __construct(RepositoryService $repositoryService, array $providers = ['gitlab', 'github'])
    {
        $this->repositoryService = $repositoryService;
        $this->providers = $providers;
    }

    public function search(RepositorySearchRequest $request)
    {

        $repositories = [];
        
        // We validate our query input data using the rules specified in the RepositorySearchRequest class
        $query = $request->validated()['q'];

        // This loop will allow us to fetch repositories from as many providers as possible, if an error is found the app will still function properly and log the error
        foreach ($this->providers as $provider) {
            try {
                $providerRepositories = $this->repositoryService->search($query, $provider);
                $repositories = array_merge($repositories, $providerRepositories);
            } catch (Exception $e) {
                // To handle the exception we simply log the error and the provider that caused it.
                Log::error('ERROR : '. $e->getMessage(), ['provider' => $provider]);
            }
        }

        return $repositories;
    }
}
