<?php
require 'execute.php';
$branch = $_GET['name'] ?? '';
if (empty($branch)) {
    echo json_encode(['error' => 'Branch name required']);
    exit;
}
chdir(__DIR__ . '/');
$output = git_exec('git checkout -b ' . escapeshellarg($branch));
git_exec('git push -u origin ' . escapeshellarg($branch));
echo json_encode(['output' => $output]);
?>
