<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/execute.php';
chdir(__DIR__ . '/../');

$output = git_exec('git branch --format="%(refname:short)"');
if (!$output) {
    echo json_encode(['error' => 'Failed to get branches']);
    exit;
}

// Parse branches into array
$branches = array_filter(array_map('trim', explode("\n", $output)));

echo json_encode(['branches' => $branches]);
