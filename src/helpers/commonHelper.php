<?php

function renameDirFile($dirPath,$newName) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    if (!empty($files)) {
        foreach ($files as $file) {
            if (!is_dir($file)) {
                rename($file,$dirPath.'/'.$newName.'.txt');
            }
        }
    }
}