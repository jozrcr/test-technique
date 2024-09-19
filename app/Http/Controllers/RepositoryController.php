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

    public function __construct(RepositoryService $repositoryService)
    {
        $this->repositoryService = $repositoryService;
    }

    public function search(RepositorySearchRequest $request)
    {

        $repositories = [];
        
        // We validate our query input data using the rules specified in the RepositorySearchRequest class
        $query = $request->validated()['q'];

        try {
            $repositories = $this->repositoryService->search($query);
        } catch (Exception $e) {
            // Handle the exception
        }

        return $repositories;
    }
}
