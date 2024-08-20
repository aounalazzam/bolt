<?php

namespace Bolt\Lib\Routing;

class RouteRequest
{
    public array $body = [];
    public array $query = [];

    function __construct(array $requestedData)
    {
        $this->body = $requestedData['body'] ?? [];
        $this->query = $requestedData['query'] ?? [];
    }
}
