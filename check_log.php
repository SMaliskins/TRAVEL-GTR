<?php
/**
 * Simple script to check if logging works
 * Just open this file in browser: check_log.php
 */

// Test log.php directly
$testUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/log.php?test=1';

echo "<h1>Log Check</h1>";
echo "<p>Testing log.php...</p>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<h2>Response from log.php:</h2>";
echo "<p>HTTP Code: <strong>" . $httpCode . "</strong></p>";

if ($error) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($error) . "</p>";
}

if ($response) {
    $data = json_decode($response, true);
    if ($data) {
        echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
        
        if (isset($data['success']) && $data['success']) {
            echo "<p style='color: green;'>✓ Logging works!</p>";
        } else {
            echo "<p style='color: red;'>✗ Logging failed: " . htmlspecialchars($data['message'] ?? 'Unknown error') . "</p>";
            if (isset($data['debug'])) {
                echo "<h3>Debug Info:</h3>";
                echo "<pre>" . json_encode($data['debug'], JSON_PRETTY_PRINT) . "</pre>";
            }
        }
    } else {
        echo "<p>Response: " . htmlspecialchars($response) . "</p>";
    }
} else {
    echo "<p style='color: red;'>No response from log.php</p>";
}

// Check log file
$logFile = __DIR__ . '/log.txt';
echo "<h2>Log File Status</h2>";
echo "<p>File path: <code>" . htmlspecialchars($logFile) . "</code></p>";

if (file_exists($logFile)) {
    echo "<p style='color: green;'>✓ File exists</p>";
    echo "<p>Size: " . filesize($logFile) . " bytes</p>";
    echo "<p>Readable: " . (is_readable($logFile) ? 'Yes' : 'No') . "</p>";
    echo "<p>Writable: " . (is_writable($logFile) ? 'Yes' : 'No') . "</p>";
    
    // Show last 10 lines
    $lines = file($logFile);
    if ($lines && count($lines) > 0) {
        echo "<h3>Last 10 log entries:</h3>";
        echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd; max-height: 400px; overflow: auto;'>";
        echo htmlspecialchars(implode('', array_slice($lines, -10)));
        echo "</pre>";
    } else {
        echo "<p style='color: orange;'>⚠ File exists but is empty</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠ File does not exist</p>";
    echo "<p>Directory: <code>" . htmlspecialchars(dirname($logFile)) . "</code></p>";
    echo "<p>Directory exists: " . (is_dir(dirname($logFile)) ? 'Yes' : 'No') . "</p>";
    echo "<p>Directory writable: " . (is_writable(dirname($logFile)) ? 'Yes' : 'No') . "</p>";
}

// Try to create test entry directly
echo "<h2>Direct File Write Test</h2>";
$testEntry = "[" . date('Y-m-d H:i:s') . "] TEST ENTRY - Direct write test\n";
$writeResult = @file_put_contents($logFile, $testEntry, FILE_APPEND | LOCK_EX);

if ($writeResult !== false) {
    echo "<p style='color: green;'>✓ Direct write successful (" . $writeResult . " bytes written)</p>";
} else {
    echo "<p style='color: red;'>✗ Direct write failed</p>";
    $lastError = error_get_last();
    if ($lastError) {
        echo "<p>Error: " . htmlspecialchars($lastError['message']) . "</p>";
    }
}

