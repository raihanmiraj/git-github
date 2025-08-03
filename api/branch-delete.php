<?php
require 'execute.php';  // your helper to run shell commands

$branch = $_GET['name'] ?? '';
$branch = trim($branch);

if (!$branch) {
    echo json_encode(['error' => 'Branch name is required']);
    exit;
}

// Sanitize branch name to avoid injection
if (!preg_match('/^[\w\-\/]+$/', $branch)) {
    echo json_encode(['error' => 'Invalid branch name']);
    exit;
}

chdir(__DIR__ . '/../');  // path to your git repo root

// Delete remote branch
$cmd = "git push origin --delete " . escapeshellarg($branch) . " 2>&1";
$output = git_exec($cmd);

echo json_encode([
    'output' => $output,
]);
