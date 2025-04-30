<?php
function getRoutes($dir, $baseRoute = '') {
    $routes = [];
    $items = scandir($dir);

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;

        $fullPath = "$dir/$item";

        if (is_dir($fullPath)) {
            $newBase = $baseRoute . '/' . $item;
            $routes = array_merge($routes, getRoutes($fullPath, $newBase));
        } elseif (pathinfo($item, PATHINFO_EXTENSION) === 'php') {
            if ($item === 'config.php' || $item === 'generate_routes.php') continue;

            $routePath = $baseRoute . '/' . $item;
            $routes[] = [
                'path' => str_replace('//', '/', $routePath),
                'file' => $fullPath
            ];
        }
    }

    return $routes;
}

$root = __DIR__;
$routes = getRoutes($root);

header('Content-Type: application/json');
echo json_encode($routes, JSON_PRETTY_PRINT);
