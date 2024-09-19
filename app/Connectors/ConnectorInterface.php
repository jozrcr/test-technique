<?php

namespace App\Connectors;

interface ConnectorInterface
{
    public function search(string $query) : array;
}