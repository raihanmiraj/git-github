<?php
function git_exec($command) {
    putenv('GIT_SSH_COMMAND=ssh -i ' . __DIR__ . '/../.ssh/deploykey -o IdentitiesOnly=yes');
    $output = shell_exec($command . ' 2>&1');
    return $output;
}
?>
