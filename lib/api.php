<?php

include_once "./lib/validation.php";

class RouteRequestData
{
    public array $body = [];
    public array $query = [];

    function __construct(array $requestedData)
    {
        $this->body = $requestedData['body'] ?? [];
        $this->query = $requestedData['query'] ?? [];
    }
}

function writeResponse($data, $statusCode = 200)
{
    header("Content-type: application/json");
    http_response_code($statusCode);
    echo json_encode($data);
    die;
}

function createAPIHandler(string $methodName, callable $handler, array $validationSchemaInputs): void
{
    if (
        !($_SERVER['REQUEST_METHOD'] === $methodName)
    ) {
        return;
    }

    $requestedBody = [];
    $requestedQuery = $_GET;

    if ($methodName === "POST") {
        $RAW_POST_DATA = json_decode(file_get_contents('php://input'), true);
        $requestedBody = (count($_POST) > 0) ? $_POST : $RAW_POST_DATA;
    }

    try {
        if (count($validationSchemaInputs) > 0) {
            $validationSchema = [];

            foreach ($validationSchemaInputs as $key => $values) {
                for ($i = 0; $i < count($values); $i++) {
                    [$idKey, $idVal] = explode("_", $values[$i]->value);
                    $validationSchema[$key][$idKey] = $idVal;
                }
            }

            if ($methodName === "POST") {
                validateSchema($validationSchema, $requestedBody);
            }

            if ($methodName === "GET") {
                validateSchema($validationSchema, $requestedQuery);
            }
        }

        $routeRequestData = new RouteRequestData([
            'body' => $requestedBody,
            'query' => $requestedQuery
        ]);

        $res = $handler($routeRequestData);

        if (gettype($res) === "string") {
            if ($res === "redirect") exit;
        } else {
            writeResponse($res);
        }
    } catch (ClientException | UnauthorizedException | ForbiddenException | NotFoundException | ConflictException | InternalServerErrorException $th) {
        writeResponse([
            "message" => $th->getMessage(),
        ], $th->getCode());
    } catch (\Throwable $th) {
        writeResponse([
            "message" => $th->getMessage(),
        ], 500);
    }
}


class API
{
    static function post(callable $handler, array $validationSchema = []): void
    {
        createAPIHandler("POST", $handler, $validationSchema);
    }

    static function get(callable $handler, array $validationSchema = []): void
    {
        createAPIHandler("GET", $handler, $validationSchema);
    }
}
