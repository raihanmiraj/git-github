<?php
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
