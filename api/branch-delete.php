<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/execute.php';

if (!isset($_GET['name']) || empty(trim($_GET['name']))) {
    echo json_encode(['error' => 'Branch name is required']);
    exit;
}

$branch = escapeshellarg(trim($_GET['name']));

// Change to repo root (adjust if needed)
chdir(__DIR__ . '/../');

$output = git_exec("git branch -D $branch 2>&1");

// Return output as JSON
echo json_encode(['output' => $output]);
