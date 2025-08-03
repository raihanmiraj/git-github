<?php
require 'execute.php';
$branch = $_GET['name'] ?? '';
if (empty($branch)) {
    echo json_encode(['error' => 'Branch name required']);
    exit;
}
chdir(__DIR__ . '/../repo');
$output = git_exec('git checkout ' . escapeshellarg($branch));
 
echo json_encode([
    'status' => 'success',
    'output' => $output
]);
