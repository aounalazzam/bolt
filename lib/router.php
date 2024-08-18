<?php
include_once "./utils/file.php";
include_once "./lib/minify.php";
include_once "./lib/templating.php";
include_once "./lib/config.php";

class Router
{
    static private array $route;
    static private array $queryParams;
    static private string $routesDirPath = "./routes";

    private static function handleSitemapXML()
    {
        ["path" => $path] = Router::getPathUrl();

        $sitemapPagePath = self::$routesDirPath . "/sitemap.php";

        if ($path === "/sitemap.xml" && file_exists($sitemapPagePath)) {
            include_once $sitemapPagePath;
            exit;
        }
    }

    static function run()
    {
        $url = Router::getPathUrl();
        $routes = Router::getRoutes(self::$routesDirPath);

        Router::runMiddleware();

        $routeFound = false;

        // Handle Sitemap.xml by php
        Router::handleSitemapXML();

        // Handle Routes
        foreach ($routes as $routePath => $route) {
            $regexPath = Router::pathToRegex($routePath);
            $isMatched = preg_match($regexPath, $url['path'], $matches);

            // echo $routePath . " => " . $isMatched . "<br/>";

            if ($isMatched) {
                $routeFound = true;

                Router::$route = [
                    'path' => $routePath,
                    "result" => $regexPath,
                    "matches" => $matches
                ];
                Router::$queryParams = $url['query'];

                $fullPhpFilePath = self::$routesDirPath . $route;

                // Static File
                if (!str_contains($fullPhpFilePath, ".php")) {
                    // TODO
                    exit;
                }

                include_once $fullPhpFilePath;
                exit;
            }
        }

        // Handle 404 Page Not Found
        if (!$routeFound) {
            header('HTTP/1.1 404 Not Found');
        }
    }

    static function runMiddleware()
    {
        $middlewarePath = "./middleware/index.php";

        if (file_exists($middlewarePath)) {
            include_once $middlewarePath;
        }
    }

    static private function getPathUrl()
    {
        $urlPathname = $_SERVER['REQUEST_URI'];
        $urlPathname = str_replace("/rawa-new-website", "", $urlPathname);
        $urlComponents = parse_url($urlPathname);

        $path = $urlComponents['path'];
        $query = [];

        if (isset($urlComponents['query'])) {
            parse_str($urlComponents['query'], $query);
        }

        return ['path' => $path, 'query' => $query];
    }

    static private function getRoutes(string $dir, string $rootDir = "")
    {
        $files = scandir($dir);
        $routes = [];

        foreach ($files as $fileName) {
            if ($fileName == "." || $fileName == "..") {
                continue;
            }

            $pathname = "$dir/$fileName";

            if (is_dir($pathname)) {
                $routes = [
                    ...$routes,
                    ...self::getRoutes($pathname, "$rootDir/$fileName")
                ];
            } else {
                $filePath = "$rootDir/$fileName";
                $pathname = str_replace(".php", "", $filePath);
                $pathname = str_replace("index", "", $pathname);

                $routes[$pathname] = $filePath;
            }
        }

        return $routes;
    }

    static function getParams()
    {
        return array_slice(Router::$route['matches'], 1);
    }

    static function getQueryParams()
    {
        return Router::$queryParams;
    }

    static private function pathToRegex($path)
    {
        $regex = preg_replace("/\//", "\\/", $path);
        $regex = preg_replace("/\[\w+\]/", "(.+)", $regex);
        return "/^" . str_replace(" ", "", $regex) . "$/i";
    }
}
