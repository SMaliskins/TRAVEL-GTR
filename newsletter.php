<?php
/**
 * Newsletter subscription handler
 * Saves email addresses to a file
 */

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get data from POST request
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    $data = $_POST;
}

$email = isset($data['email']) ? trim($data['email']) : '';
$language = isset($data['language']) ? $data['language'] : 'lv';
$consent = isset($data['consent']) ? $data['consent'] : false;

// Validate email
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email address'
    ]);
    exit;
}

// Check consent
if (!$consent) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Consent is required'
    ]);
    exit;
}

// Prepare log entry
$timestamp = date('Y-m-d H:i:s');
$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unknown';
$userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown';

$entry = sprintf(
    "[%s] Email: %s | Language: %s | IP: %s | User-Agent: %s\n",
    $timestamp,
    $email,
    $language,
    $ip,
    $userAgent
);

// Save to file
$newsletterFile = __DIR__ . '/newsletter_subscribers.txt';
$newsletterDir = dirname($newsletterFile);

// Ensure directory exists and is writable
if (!is_dir($newsletterDir)) {
    @mkdir($newsletterDir, 0755, true);
}

// Check if directory is writable
if (!is_writable($newsletterDir)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Directory is not writable. Please check permissions.'
    ]);
    exit;
}

// Try to create file if it doesn't exist
if (!file_exists($newsletterFile)) {
    $header = "=== Newsletter Subscribers ===\nCreated: " . date('Y-m-d H:i:s') . "\n\n";
    $createResult = @file_put_contents($newsletterFile, $header, LOCK_EX);
    if ($createResult === false) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create newsletter file. Please check permissions.'
        ]);
        exit;
    }
}

// Check if email already exists
$existingEmails = [];
if (file_exists($newsletterFile) && is_readable($newsletterFile)) {
    $lines = @file($newsletterFile);
    if ($lines !== false) {
        foreach ($lines as $line) {
            if (preg_match('/Email: ([^\s|]+)/', $line, $matches)) {
                $existingEmails[] = strtolower(trim($matches[1]));
            }
        }
    }
}

// Check for duplicate
if (in_array(strtolower($email), $existingEmails)) {
    echo json_encode([
        'success' => true,
        'message' => 'Email already subscribed',
        'duplicate' => true
    ]);
    exit;
}

// Write to file
$result = @file_put_contents($newsletterFile, $entry, FILE_APPEND | LOCK_EX);

if ($result !== false) {
    // Send notification email to info@gtr.lv
    require_once __DIR__ . '/config.php';
    $notificationEmail = SITE_EMAIL;
    $subject = 'New Newsletter Subscription - ' . $email;
    
    $message = "New subscriber added to newsletter:\n\n";
    $message .= "Email: " . $email . "\n";
    $message .= "Language: " . $language . "\n";
    $message .= "Date: " . $timestamp . "\n";
    $message .= "IP Address: " . $ip . "\n";
    $message .= "User-Agent: " . $userAgent . "\n\n";
    $message .= "---\n";
    $message .= "This is an automated notification from " . SITE_NAME . " newsletter subscription system.";
    
    $headers = "From: newsletter@" . $_SERVER['HTTP_HOST'] . "\r\n";
    $headers .= "Reply-To: newsletter@" . $_SERVER['HTTP_HOST'] . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    // Send email (silently fail if mail function is not available)
    @mail($notificationEmail, $subject, $message, $headers);
    
    echo json_encode([
        'success' => true,
        'message' => 'Successfully subscribed to newsletter',
        'email' => $email
    ]);
} else {
    // Get last error for debugging
    $lastError = error_get_last();
    $errorMsg = 'Failed to save subscription';
    
    if ($lastError && strpos($lastError['message'], 'Permission denied') !== false) {
        $errorMsg = 'Permission denied. Please check file permissions.';
    } elseif ($lastError && strpos($lastError['message'], 'No space left') !== false) {
        $errorMsg = 'No space left on server.';
    } elseif ($lastError) {
        $errorMsg = 'Error: ' . $lastError['message'];
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $errorMsg,
        'debug' => [
            'file' => $newsletterFile,
            'writable' => is_writable($newsletterFile),
            'dir_writable' => is_writable($newsletterDir),
            'last_error' => $lastError
        ]
    ]);
}

