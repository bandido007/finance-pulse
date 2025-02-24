<?php
// public/index.php

// Get the requested path
$request = $_SERVER['REQUEST_URI'];

// Route requests
switch ($request) {
    case '/':
        require __DIR__ . '/../views/index.php';
        break;
    case '/expenses':
        require __DIR__ . '/../views/expenses.php';
        break;
    default:
        http_response_code(404);
        echo "404 Not Found, Venga Benga";
        break;
}
?>