<?php
/**
 * Admin Panel Configuration
 */

// Security: Prevent direct access
if (!defined('ADMIN_ACCESS')) {
    define('ADMIN_ACCESS', true);
}

// Admin credentials (CHANGE THESE!)
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD_HASH', password_hash('admin123', PASSWORD_DEFAULT)); // Change password!

// Session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

// Base paths
define('ADMIN_BASE', __DIR__);
define('SITE_BASE', dirname(ADMIN_BASE));
define('DATA_DIR', ADMIN_BASE . '/data');

// Create data directory if it doesn't exist
if (!is_dir(DATA_DIR)) {
    @mkdir(DATA_DIR, 0755, true);
}

// Data files
define('MENU_FILE', DATA_DIR . '/menu.json');
define('PAGES_FILE', DATA_DIR . '/pages.json');
define('TRANSLATIONS_FILE', DATA_DIR . '/translations.json');
define('SETTINGS_FILE', DATA_DIR . '/settings.json');

// Default data structure
function getDefaultMenu() {
    return [
        ['id' => 1, 'label_en' => 'Home', 'label_lv' => 'Sākums', 'label_ru' => 'Главная', 'url' => '#home', 'order' => 1],
        ['id' => 2, 'label_en' => 'Services', 'label_lv' => 'Pakalpojumi', 'label_ru' => 'Услуги', 'url' => '#services', 'order' => 2],
        ['id' => 3, 'label_en' => 'About', 'label_lv' => 'Par mums', 'label_ru' => 'О нас', 'url' => '#about', 'order' => 3],
        ['id' => 4, 'label_en' => 'Contact', 'label_lv' => 'Kontakti', 'label_ru' => 'Контакты', 'url' => '#contact', 'order' => 4]
    ];
}

function getDefaultPages() {
    return [];
}

function getDefaultTranslations() {
    return [
        'en' => [],
        'lv' => [],
        'ru' => []
    ];
}

function getDefaultSettings() {
    return [
        'site_name' => 'Gulliver Travel',
        'logo_height' => '60px',
        'header_padding' => '25px 40px',
        'menu_gap' => '35px',
        'primary_color' => '#007BFF',
        'header_shadow' => '0 2px 10px rgba(0, 0, 0, 0.05)'
    ];
}

// Initialize data files if they don't exist
function initializeDataFiles() {
    if (!file_exists(MENU_FILE)) {
        file_put_contents(MENU_FILE, json_encode(getDefaultMenu(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    if (!file_exists(PAGES_FILE)) {
        file_put_contents(PAGES_FILE, json_encode(getDefaultPages(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    if (!file_exists(TRANSLATIONS_FILE)) {
        file_put_contents(TRANSLATIONS_FILE, json_encode(getDefaultTranslations(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    if (!file_exists(SETTINGS_FILE)) {
        file_put_contents(SETTINGS_FILE, json_encode(getDefaultSettings(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

// Check if user is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Login function
function adminLogin($username, $password) {
    if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        return true;
    }
    return false;
}

// Logout function
function adminLogout() {
    session_destroy();
    session_start();
}

// Initialize data files
initializeDataFiles();

