<?php
require __DIR__ . '/execute.php';

$branch = $_GET['name'] ?? '';
$branch = trim($branch);

if (!$branch) {
    echo json_encode(['error' => 'Branch name is required']);
    exit;
}

// Sanitize branch name
if (!preg_match('/^[\w\-\/]+$/', $branch)) {
    echo json_encode(['error' => 'Invalid branch name']);
    exit;
}

chdir(__DIR__ . '/../');

// Get current branch
$currentBranch = trim(shell_exec('git rev-parse --abbrev-ref HEAD'));

// If trying to delete current branch, switch to main first
if ($currentBranch === $branch) {
    // You can customize this to your default branch
    $defaultBranch = 'main';
    $switchResult = git_exec("git checkout " . escapeshellarg($defaultBranch));
    if (strpos($switchResult, 'error') !== false) {
        echo json_encode(['error' => "Failed to switch to $defaultBranch before deleting branch."]);
        exit;
    }
}

// Delete local branch
$deleteLocal = git_exec("git branch -D " . escapeshellarg($branch) . " 2>&1");

// Delete remote branch
$deleteRemote = git_exec("git push origin --delete " . escapeshellarg($branch) . " 2>&1");

echo json_encode([
    'output' => "Switched branch if needed:\n$currentBranch -> $defaultBranch\n\nLocal delete:\n$deleteLocal\n\nRemote delete:\n$deleteRemote",
]);
