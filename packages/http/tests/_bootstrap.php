<?php

$pathToAutoloader = codecept_root_dir('vendor/autoload.php');

if (!file_exists($pathToAutoloader)) {
    $pathToAutoloader = codecept_root_dir('../../vendor/autoload.php');
}

require_once $pathToAutoloader;

