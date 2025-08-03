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

$results = [];

// Check current branch
$currentBranch = trim(git_exec('git rev-parse --abbrev-ref HEAD'));

// --- Delete Local Branch ---
$localBranches = explode("\n", git_exec('git branch'));
$localBranches = array_map(function($b) { return trim(ltrim($b, '* ')); }, $localBranches);

if (in_array($branch, $localBranches)) {
    if ($currentBranch === $branch) {
        $results['local'] = "Cannot delete local branch '$branch' because it is currently checked out.";
    } else {
        $localDelete = git_exec("git branch -D " . escapeshellarg($branch) . " 2>&1");
        $results['local'] = $localDelete;
    }
} else {
    $results['local'] = "Branch '$branch' does not exist locally.";
}

// --- Delete Remote Branch ---
git_exec('git fetch --all 2>&1');  // Make sure remotes are updated
$remoteBranches = explode("\n", git_exec('git branch -r'));
$remoteBranches = array_map(function($b) {
    $b = trim($b);
    if (strpos($b, 'origin/') === 0) {
        return substr($b, 7);
    }
    return $b;
}, $remoteBranches);

if (in_array($branch, $remoteBranches)) {
    $remoteDelete = git_exec("git push origin --delete " . escapeshellarg($branch) . " 2>&1");
    $results['remote'] = $remoteDelete;
} else {
    $results['remote'] = "Branch '$branch' does not exist on remote.";
}

echo json_encode($results);
?>
