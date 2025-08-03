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

// First, check current branch
$currentBranch = trim(git_exec('git rev-parse --abbrev-ref HEAD'));

if ($currentBranch === $branch) {
    echo json_encode(['error' => "Cannot delete the branch '$branch' as it is currently checked out. Please switch to another branch first."]);
    exit;
}

// Delete remote branch
$remoteDelete = git_exec("git push origin --delete " . escapeshellarg($branch) . " 2>&1");

// Delete local branch
$localDelete = git_exec("git branch -D " . escapeshellarg($branch) . " 2>&1");

echo json_encode([
    'remote_delete' => $remoteDelete,
    'local_delete' => $localDelete,
]);
?>
