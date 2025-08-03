<?php

header("Access-Control-Allow-Origin: *"); // You can replace * with your frontend domain for security
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight requests (OPTIONS method)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

$request = $_GET['action'] ?? '';

switch ($request) {
    case 'pull':
        require 'api/pull.php';
        break;
    case 'push':
        require 'api/push.php';
        break;
    case 'branch-create':
        require 'api/branch-create.php';
        break;
    case 'branch-switch':
        require 'api/branch-switch.php';
        break;
    case 'branch-delete':
        require 'api/branch-delete.php';
        break;
    case 'branch-list':
        require 'api/branch-list.php';
        break;

    default:
        echo json_encode(['error' => 'Invalid Action']);
}
