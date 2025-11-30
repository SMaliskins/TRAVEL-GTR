<?php
/**
 * Site Configuration
 * Automatically detects the base URL and provides helper functions
 * This file is safe to include from any directory
 */

// Only define if not already defined (to prevent conflicts)
if (!defined('SITE_CONFIG_LOADED')) {
    define('SITE_CONFIG_LOADED', true);
    
    // Detect protocol (http or https)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    
    // Detect hostname
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Detect base path (if site is in subdirectory)
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $basePath = str_replace('\\', '/', dirname($scriptName));
    if ($basePath === '/' || $basePath === '\\') {
        $basePath = '';
    }
    
    // Base URL
    if (!defined('BASE_URL')) {
        define('BASE_URL', $protocol . $host . $basePath);
    }
    if (!defined('BASE_PATH')) {
        define('BASE_PATH', $basePath);
    }
    
    // Helper function to get base URL
    if (!function_exists('getBaseUrl')) {
        function getBaseUrl() {
            return BASE_URL;
        }
    }
    
    // Helper function to get asset URL (for images, CSS, JS)
    if (!function_exists('getAssetUrl')) {
        function getAssetUrl($path) {
            $path = ltrim($path, '/');
            return BASE_URL . '/' . $path;
        }
    }
    
    // Helper function to get admin URL
    if (!function_exists('getAdminUrl')) {
        function getAdminUrl($path = '') {
            $path = ltrim($path, '/');
            return BASE_URL . '/admin/' . $path;
        }
    }
    
    // Site settings
    if (!defined('SITE_NAME')) {
        define('SITE_NAME', 'Gulliver Travel');
    }
    if (!defined('SITE_EMAIL')) {
        define('SITE_EMAIL', 'info@gtr.lv');
    }
    if (!defined('WHATSAPP_NUMBER')) {
        define('WHATSAPP_NUMBER', '37122306472');
    }
    
    // Waavo settings (these might need to stay as is, but we'll make them configurable)
    if (!defined('WAAVO_HOST')) {
        define('WAAVO_HOST', 'gtrlv.waavo.com');
    }
}

