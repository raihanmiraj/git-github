<?php
require 'execute.php';
chdir(__DIR__ . '/../repo');
git_exec('git add .');
git_exec('git commit -m "Auto commit from PHP API"');
$output = git_exec('git push');
echo json_encode(['output' => $output]);
?>
