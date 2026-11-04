<?php
$requestUri = $_SERVER['REQUEST_URI'];
$_SERVER['HOME'] = __DIR__ . '/tmp';

if (preg_match('/.*v4\//', $requestUri)) {
    return require_once 'public/index.php';
}

if (preg_match('/rest\/v1\//', $requestUri)) {
    return require_once 'public/index.php';
}

return require('app.php');
