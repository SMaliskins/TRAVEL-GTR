<?php
/**
 * Logging script for travel.gtr.lv
 * Logs: IP, referrer, device, browser, load time, URL
 */

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Get client IP address
function getClientIP() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

// Parse User-Agent to get browser and device info
function parseUserAgent($userAgent) {
    $browser = 'Unknown';
    $device = 'Desktop';
    $os = 'Unknown';
    
    // Detect OS
    if (preg_match('/windows/i', $userAgent)) {
        $os = 'Windows';
    } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
        $os = 'Mac OS';
    } elseif (preg_match('/linux/i', $userAgent)) {
        $os = 'Linux';
    } elseif (preg_match('/android/i', $userAgent)) {
        $os = 'Android';
        $device = 'Mobile';
    } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
        $os = 'iOS';
        $device = preg_match('/ipad/i', $userAgent) ? 'Tablet' : 'Mobile';
    }
    
    // Detect Browser
    if (preg_match('/firefox/i', $userAgent)) {
        $browser = 'Firefox';
        if (preg_match('/firefox\/([0-9.]+)/i', $userAgent, $matches)) {
            $browser .= ' ' . $matches[1];
        }
    } elseif (preg_match('/chrome/i', $userAgent) && !preg_match('/edg|opr/i', $userAgent)) {
        $browser = 'Chrome';
        if (preg_match('/chrome\/([0-9.]+)/i', $userAgent, $matches)) {
            $browser .= ' ' . $matches[1];
        }
    } elseif (preg_match('/safari/i', $userAgent) && !preg_match('/chrome/i', $userAgent)) {
        $browser = 'Safari';
        if (preg_match('/version\/([0-9.]+)/i', $userAgent, $matches)) {
            $browser .= ' ' . $matches[1];
        }
    } elseif (preg_match('/edg/i', $userAgent)) {
        $browser = 'Edge';
        if (preg_match('/edg\/([0-9.]+)/i', $userAgent, $matches)) {
            $browser .= ' ' . $matches[1];
        }
    } elseif (preg_match('/opr|opera/i', $userAgent)) {
        $browser = 'Opera';
        if (preg_match('/opr\/([0-9.]+)/i', $userAgent, $matches)) {
            $browser .= ' ' . $matches[1];
        }
    }
    
    return [
        'browser' => $browser,
        'device' => $device,
        'os' => $os
    ];
}

// Handle GET request for testing
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['test'])) {
    // Create a test log entry
    $data = [
        'loadTime' => 1234,
        'domContentLoaded' => 800,
        'firstPaint' => 1200,
        'firstContentfulPaint' => 1200,
        'url' => $_SERVER['REQUEST_URI'],
        'screenSize' => '1920x1080',
        'language' => 'lv',
        'connectionType' => '4g',
        'connectionSpeed' => '10 Mbps'
    ];
} else {
    // Get data from POST request
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // If JSON decode failed, try to get from $_POST
    if ($data === null && !empty($_POST)) {
        $data = $_POST;
    }
}

// Prepare log entry
$timestamp = date('Y-m-d H:i:s');
$ip = getClientIP();
$referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Direct';
$userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown';

// Parse user agent
$uaInfo = parseUserAgent($userAgent);

// Get additional data from JavaScript (or use defaults)
$loadTime = isset($data['loadTime']) && $data['loadTime'] !== 'N/A' ? $data['loadTime'] : 'N/A';
$domContentLoaded = isset($data['domContentLoaded']) && $data['domContentLoaded'] !== 'N/A' ? $data['domContentLoaded'] : 'N/A';
$firstPaint = isset($data['firstPaint']) && $data['firstPaint'] !== 'N/A' ? $data['firstPaint'] : 'N/A';
$firstContentfulPaint = isset($data['firstContentfulPaint']) && $data['firstContentfulPaint'] !== 'N/A' ? $data['firstContentfulPaint'] : 'N/A';
$url = isset($data['url']) ? $data['url'] : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI']);
$screenSize = isset($data['screenSize']) ? $data['screenSize'] : 'N/A';
$language = isset($data['language']) ? $data['language'] : (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : 'N/A');
$connectionType = isset($data['connectionType']) ? $data['connectionType'] : 'N/A';
$connectionSpeed = isset($data['connectionSpeed']) ? $data['connectionSpeed'] : 'N/A';

// Format log entry with performance metrics
$logEntry = sprintf(
    "[%s] IP: %s | Referrer: %s | Browser: %s | OS: %s | Device: %s | Load: %s ms | DOM: %s ms | FCP: %s ms | Screen: %s | Lang: %s | Connection: %s (%s) | URL: %s\n",
    $timestamp,
    $ip,
    $referrer,
    $uaInfo['browser'],
    $uaInfo['os'],
    $uaInfo['device'],
    $loadTime,
    $domContentLoaded,
    $firstContentfulPaint !== 'N/A' ? $firstContentfulPaint : $firstPaint,
    $screenSize,
    $language,
    $connectionType,
    $connectionSpeed,
    $url
);

// Write to log file
$logFile = __DIR__ . '/log.txt';

// Create log file if it doesn't exist
if (!file_exists($logFile)) {
    file_put_contents($logFile, "=== Travel GTR Log File ===\nCreated: " . date('Y-m-d H:i:s') . "\n\n", LOCK_EX);
}

// Rotate log if it's larger than 10MB
if (file_exists($logFile) && filesize($logFile) > 10 * 1024 * 1024) {
    $backupFile = __DIR__ . '/log_' . date('Y-m-d_His') . '.txt';
    rename($logFile, $backupFile);
    file_put_contents($logFile, "=== Travel GTR Log File ===\nCreated: " . date('Y-m-d H:i:s') . "\n\n", LOCK_EX);
}

// Write log entry
$result = false;
$errorMsg = '';

// Try to write to log file
try {
    // Ensure directory exists and is writable
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    
    // Try to write
    $result = @file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    
    if ($result === false) {
        // Try to create file if it doesn't exist
        if (!file_exists($logFile)) {
            $header = "=== Travel GTR Log File ===\nCreated: " . date('Y-m-d H:i:s') . "\n\n";
            $result = @file_put_contents($logFile, $header . $logEntry, LOCK_EX);
        }
        
        if ($result === false) {
            // Check permissions
            if (!is_writable($logDir)) {
                $errorMsg = 'Directory not writable: ' . $logDir;
            } elseif (file_exists($logFile) && !is_writable($logFile)) {
                $errorMsg = 'File not writable: ' . $logFile;
            } else {
                $errorMsg = 'Failed to write log file. Check permissions.';
            }
        }
    }
} catch (Exception $e) {
    $errorMsg = 'Exception: ' . $e->getMessage();
}

// Check if write was successful
$success = $result !== false;
$message = $success ? 'Logged successfully' : ($errorMsg ?: 'Failed to write log');

// Return response
header('Content-Type: application/json');
$response = [
    'success' => $success,
    'message' => $message
];

// Add debug info if failed
if (!$success) {
    $response['debug'] = [
        'logFile' => $logFile,
        'logDir' => dirname($logFile),
        'dirExists' => is_dir(dirname($logFile)),
        'dirWritable' => is_writable(dirname($logFile)),
        'fileExists' => file_exists($logFile),
        'fileWritable' => file_exists($logFile) ? is_writable($logFile) : false
    ];
}

echo json_encode($response);

