<?php
require 'execute.php';  // your helper to run shell commands

$branch = $_GET['name'] ?? '';
$branch = trim($branch);

if (!$branch) {
    echo json_encode(['error' => 'Branch name is required']);
    exit;
}

// Protect main branch from deletion
if ($branch === 'main') {
    echo json_encode(['error' => 'You cannot delete the main branch.']);
    exit;
}

// Sanitize branch name to avoid injection
if (!preg_match('/^[\w\-\/]+$/', $branch)) {
    echo json_encode(['error' => 'Invalid branch name']);
    exit;
}

chdir(__DIR__ . '/../');  // path to your git repo root

$results = [];

// --- Always Check Current Branch First ---
$currentBranch = trim(git_exec('git rev-parse --abbrev-ref HEAD'));

if ($currentBranch === $branch) {
    // Switch to main branch before deleting
    $checkout = git_exec('git checkout main 2>&1');
    $results['checkout'] = $checkout;

    // Verify switch was successful
    $newCurrentBranch = trim(git_exec('git rev-parse --abbrev-ref HEAD'));
    if ($newCurrentBranch !== 'main') {
        echo json_encode(['error' => 'Failed to switch to main branch before deletion.', 'details' => $checkout]);
        exit;
    }
}

// --- Delete Local Branch ---
$localBranches = explode("\n", git_exec('git branch'));
$localBranches = array_map(function ($b) {
    return trim(ltrim($b, '* '));
}, $localBranches);

if (in_array($branch, $localBranches)) {
    $localDelete = git_exec("git branch -D " . escapeshellarg($branch) . " 2>&1");
    $results['local'] = $localDelete;
} else {
    $results['local'] = "Branch '$branch' does not exist locally.";
}

// --- Delete Remote Branch ---
git_exec('git fetch --all 2>&1');  // Update remote branches
$remoteBranches = explode("\n", git_exec('git branch -r'));
$remoteBranches = array_map(function ($b) {
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

echo json_encode([
    'status' => 'success',
    'output' => $results
]);
