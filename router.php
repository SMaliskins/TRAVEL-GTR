<?php
/**
 * Router for DigitalOcean App Platform
 * Handles routing for PHP applications
 */

$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);

// Remove leading slash
$requestPath = ltrim($requestPath, '/');

// If it's a file that exists, serve it
if ($requestPath && file_exists(__DIR__ . '/' . $requestPath) && !is_dir(__DIR__ . '/' . $requestPath)) {
    return false; // Let PHP serve the file
}

// Handle admin routes
if (strpos($requestPath, 'admin/') === 0) {
    $adminPath = substr($requestPath, 6); // Remove 'admin/'
    
    if (empty($adminPath) || $adminPath === 'index.php') {
        require __DIR__ . '/admin/index.php';
        return true;
    }
    
    if (file_exists(__DIR__ . '/admin/' . $adminPath)) {
        require __DIR__ . '/admin/' . $adminPath;
        return true;
    }
    
    // If it's a directory, try index.php
    if (is_dir(__DIR__ . '/admin/' . $adminPath)) {
        if (file_exists(__DIR__ . '/admin/' . $adminPath . '/index.php')) {
            require __DIR__ . '/admin/' . $adminPath . '/index.php';
            return true;
        }
    }
}

// Handle root index
if (empty($requestPath) || $requestPath === 'index.html' || $requestPath === 'index.php') {
    if (file_exists(__DIR__ . '/index.html')) {
        readfile(__DIR__ . '/index.html');
        return true;
    }
}

// Default: try to serve the file
return false;

