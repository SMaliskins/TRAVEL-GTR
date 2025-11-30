<?php
require_once '../config.php';

// Check authentication
if (!isAdminLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['type']) || !isset($data['data'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$type = $data['type'];
$content = $data['data'];

// Determine file
$file = null;
switch($type) {
    case 'menu':
        $file = MENU_FILE;
        break;
    case 'pages':
        $file = PAGES_FILE;
        break;
    case 'translations':
        $file = TRANSLATIONS_FILE;
        break;
    case 'settings':
        $file = SETTINGS_FILE;
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid type']);
        exit;
}

// Save to file
$result = file_put_contents($file, json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);

if ($result !== false) {
    echo json_encode(['success' => true, 'message' => 'Data saved successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save data']);
}

