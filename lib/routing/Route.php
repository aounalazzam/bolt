<?php

namespace Bolt\Lib\Routing;

use Bolt\Lib\Routing\{RouteRequest};
use Bolt\Utils\{ServerErrorException};

class Route
{
    private static RouteRequest $routeRequest;
    private static array $middlewareCallbacks;


    private static function writeResponse(mixed $data, int $statusCode, string $contentType)
    {
        header("Content-type: $contentType");
        http_response_code($statusCode);
        echo $data;
        exit;
    }

    private static function writeJSONResponse(array|null $data, bool $success = true, int $statusCode = 200, string|null $message = null)
    {
        self::writeResponse(
            json_encode([
                "data" => $data,
                "success" => $success,
                "message" => $message
            ]),
            $statusCode,
            "application/json"
        );
    }

    private static function createAPIRouteHandler(string $methodName, callable $handler, string $contentType): void
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

            if ($contentType === "application/json") {
                $message = $res['message'] ?? null;
                unset($res['message']);
                Route::writeJSONResponse($res, true, 200, $message);
            } else {
                Route::writeResponse($res, 200, $contentType);
            }
        } catch (ServerErrorException $th) {
            Route::writeJSONResponse(null, false, $th->getCode(), $th->getMessage());
        } catch (\Throwable $th) {
            Route::writeJSONResponse(null, false, 500, $th->getMessage());
        }
    }

    static function use(callable $callback): Route
    {
        self::$middlewareCallbacks[] = $callback;

        return new self;
    }

    static function stream(callable $handler, string $contentType, array $validationSchema = []): void
    {
        self::$routeRequest = new RouteRequest();

        self::$routeRequest->validate($validationSchema);

        Route::createAPIRouteHandler("GET", $handler, $contentType);
    }

    static function post(callable $handler, array $validationSchema = [], string $contentType = "application/json"): void
    {
        self::$routeRequest = new RouteRequest();

        self::$routeRequest->validate($validationSchema);

        Route::createAPIRouteHandler("POST", $handler, $contentType);
    }

    static function get(callable $handler, array $validationSchema = [], string $contentType = "application/json"): void
    {
        self::$routeRequest = new RouteRequest();

        self::$routeRequest->validate($validationSchema);

        Route::createAPIRouteHandler("GET", $handler, $contentType);
    }
}
