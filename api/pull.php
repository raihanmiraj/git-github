<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the Git executor
require __DIR__ . '/execute.php';

// Change directory to the root of the Git repository
chdir(__DIR__ . '/../');  // <-- This goes from /api/ to root github.raihanmiraj.com/

// Execute git pull
$output = git_exec('git pull');

// Return the output as JSON
echo json_encode([
    'status' => 'success',
    'output' => $output
]);
?>
