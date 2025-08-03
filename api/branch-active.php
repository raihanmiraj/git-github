<?php

$branch = trim(shell_exec('git rev-parse --abbrev-ref HEAD'));

if ($branch) {
    echo json_encode(['active_branch' => $branch]);
} else {
    echo json_encode(['error' => 'Unable to detect branch']);
}
