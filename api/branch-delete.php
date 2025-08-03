<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/execute.php';  // Your git_exec() helper function

if (!isset($_GET['name']) || empty(trim($_GET['name']))) {
    echo json_encode(['error' => 'Branch name is required']);
    exit;
}

$branch = trim($_GET['name']);

// Sanitize branch name to avoid command injection
if (!preg_match('/^[\w\-\/]+$/', $branch)) {
    echo json_encode(['error' => 'Invalid branch name']);
    exit;
}

chdir(__DIR__ . '/../'); // Change to your git repo root folder

// Check if branch is current branch (cannot delete checked out branch)
$currentBranch = trim(shell_exec('git rev-parse --abbrev-ref HEAD'));
if ($currentBranch === $branch) {
    echo json_encode(['error' => 'Cannot delete the branch currently checked out']);
    exit;
}

// Delete local branch
$deleteLocal = git_exec("git branch -D " . escapeshellarg($branch) . " 2>&1");

// Delete remote branch (optional, comment out if you don't want)
$deleteRemote = git_exec("git push origin --delete " . escapeshellarg($branch) . " 2>&1");

echo json_encode([
    'output' => "Local delete:\n$deleteLocal\n\nRemote delete:\n$deleteRemote",
]);
