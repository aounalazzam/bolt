<?php

namespace Bolt\Lib\Routing;

use Bolt\Lib\Validator\{Validator};

class RouteRequest
{
    public array $body;
    public array $query;

    public string $method;

    function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];

        $requestedBody = [];
        $requestedQuery = $_GET;

        if ($this->method === "POST") {
            $RAW_POST_DATA = json_decode(file_get_contents('php://input'), true);
            $requestedBody = ((count($_POST) > 0) ? $_POST : $RAW_POST_DATA) ?? [];
        }

        $this->body = $requestedBody;
        $this->query = $requestedQuery;
    }

    function validate(array $validationSchemaInputs)
    {
        if (!count($validationSchemaInputs)) {
            return;
        }

        if ($this->method === "POST") {
            Validator::validateSchema($validationSchemaInputs, $this->body);
        }

        if ($this->method === "GET") {
            Validator::validateSchema($validationSchemaInputs, $this->query);
        }
    }
}
