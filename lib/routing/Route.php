<?php

namespace Bolt\Lib\Routing;

use Bolt\Utils\{ClientException, UnauthorizedException, ForbiddenException, NotFoundException, ConflictException, InternalServerErrorException};
use Bolt\Lib\Routing\{RouteRequest};
use Bolt\Lib\Validator\{Validator};

class Route
{
    private static function writeJSONResponse($data, $statusCode = 200)
    {
        header("Content-type: application/json");
        http_response_code($statusCode);
        echo json_encode($data);
        die;
    }

    private static function createAPIRouteHandler(string $methodName, callable $handler, array $validationSchemaInputs): void
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
                    Validator::validateSchema($validationSchema, $requestedBody);
                }

                if ($methodName === "GET") {
                    Validator::validateSchema($validationSchema, $requestedQuery);
                }
            }

            $routeRequestData = new RouteRequest([
                'body' => $requestedBody,
                'query' => $requestedQuery
            ]);

            $res = $handler($routeRequestData);

            if (gettype($res) === "string") {
                if ($res === "redirect") exit;
            } else {
                Route::writeJSONResponse($res);
            }
        } catch (ClientException | UnauthorizedException | ForbiddenException | NotFoundException | ConflictException | InternalServerErrorException $th) {
            Route::writeJSONResponse([
                "message" => $th->getMessage(),
            ], $th->getCode());
        } catch (\Throwable $th) {
            Route::writeJSONResponse([
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    static function post(callable $handler, array $validationSchema = []): void
    {
        Route::createAPIRouteHandler("POST", $handler, $validationSchema);
    }

    static function get(callable $handler, array $validationSchema = []): void
    {
        Route::createAPIRouteHandler("GET", $handler, $validationSchema);
    }
}
