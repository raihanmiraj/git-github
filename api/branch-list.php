<?php
require 'execute.php';  // your helper to run shell commands

chdir(__DIR__ . '/../');  // path to your git repo root

// Fetch latest remote info
git_exec('git fetch --all 2>&1');

// Get remote branches
$output = git_exec('git branch -r');

$branches = [];
foreach (explode("\n", $output) as $line) {
    $line = trim($line);
    // Remove origin/ prefix and HEAD reference
    if ($line && strpos($line, 'origin/HEAD') === false) {
        $branches[] = preg_replace('/^origin\//', '', $line);
    }
}

echo json_encode([
    'branches' => $branches,
]);
