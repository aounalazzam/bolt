<?php

namespace Bolt\Lib\Routing;

use Bolt\Lib\Routing\{RouteRequest};
use Bolt\Utils\{ServerErrorException};

class Route
{
    private static RouteRequest $routeRequest;
    private static array $middlewareCallbacks;


    private static function writeJSONResponse($data, $statusCode = 200)
    {
        header("Content-type: application/json");
        http_response_code($statusCode);
        echo json_encode($data);
        die;
    }

    private static function createAPIRouteHandler(string $methodName, callable $handler): void
    {
        if (
            !($_SERVER['REQUEST_METHOD'] === $methodName)
        ) {
            return;
        }

        try {
            self::$middlewareCallbacks ??= [];

            foreach (self::$middlewareCallbacks as $callback) {
                $callback(self::$routeRequest);
            }

            $res = $handler(self::$routeRequest);

            if (gettype($res) === "string") {
                if ($res === "redirect") exit;
            } else {
                Route::writeJSONResponse($res);
            }
        } catch (ServerErrorException $th) {
            Route::writeJSONResponse([
                "message" => $th->getMessage(),
            ], $th->getCode());
        } catch (\Throwable $th) {
            Route::writeJSONResponse([
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    static function use(callable $callback): Route
    {
        self::$middlewareCallbacks[] = $callback;

        return new self;
    }

    static function post(callable $handler, array $validationSchema = []): void
    {
        self::$routeRequest = new RouteRequest();

        self::$routeRequest->validate($validationSchema);

        Route::createAPIRouteHandler("POST", $handler);
    }

    static function get(callable $handler, array $validationSchema = []): void
    {
        self::$routeRequest = new RouteRequest();

        self::$routeRequest->validate($validationSchema);

        Route::createAPIRouteHandler("GET", $handler);
    }
}
