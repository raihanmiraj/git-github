<?php
require 'execute.php';
chdir(__DIR__ . '/../repo');
$output = git_exec('git pull');
echo json_encode(['output' => $output]);
?>
