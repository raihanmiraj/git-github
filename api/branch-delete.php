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

// --- Check and Switch if Current Branch is Being Deleted ---
$currentBranch = trim(git_exec('git rev-parse --abbrev-ref HEAD'));

if ($currentBranch === $branch) {
    $checkout = git_exec('git checkout main 2>&1');
    $results['checkout'] = $checkout;

    // Verify switch to main was successful
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
    if (strpos($localDelete, 'Deleted branch') !== false) {
        $results['local'] = $localDelete;

        // --- Now Attempt to Delete Remote Branch ---
        git_exec('git fetch --all 2>&1');  // Update remote branches
        $remoteDelete = git_exec("git push origin --delete " . escapeshellarg($branch) . " 2>&1");

        if (strpos($remoteDelete, 'remote ref does not exist') !== false || strpos($remoteDelete, 'error:') !== false) {
            $results['remote'] = "Branch '$branch' does not exist on remote.";
        } else {
            $results['remote'] = $remoteDelete;
        }

    } else {
        $results['local'] = "Failed to delete local branch '$branch'.";
    }
} else {
    $results['local'] = "Branch '$branch' does not exist locally.";
}

// Convert results array to string with newlines
$outputString = '';
if (isset($results['checkout'])) {
    $outputString .= "Checkout: " . $results['checkout'] . "\n";
}
if (isset($results['local'])) {
    $outputString .= "Local: " . $results['local'] . "\n";
}
if (isset($results['remote'])) {
    $outputString .= "Remote: " . $results['remote'] . "\n";
}

echo json_encode([
    'status' => 'success',
    'output' => $outputString
]);
