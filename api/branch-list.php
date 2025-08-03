<?php
require 'execute.php';  // your helper to run shell commands

chdir(__DIR__ . '/../');  // path to your git repo root

// Get local branches
$output = git_exec('git branch');

$branches = [];
foreach (explode("\n", $output) as $line) {
    $line = trim($line);
    if ($line) {
        // Remove * marker for current branch
        $branches[] = ltrim($line, '* ');
    }
}

echo json_encode([
    'branches' => $branches,
]);
?>
