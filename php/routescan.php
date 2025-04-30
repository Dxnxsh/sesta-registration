<?php

$dir = new RecursiveDirectoryIterator(__DIR__);
$iterator = new RecursiveIteratorIterator($dir);
$routes = [];

foreach ($iterator as $file) {
    if ($file->getExtension() === 'php') {
        $path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $file->getPathname());
        $routes[] = '/' . ltrim($path, '/');
    }
}
print_r($routes);