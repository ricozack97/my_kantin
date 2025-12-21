<?php

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
chdir(__DIR__);

/*
 |---------------------------------------------------------------
 | BOOTSTRAP THE APPLICATION
 |---------------------------------------------------------------
 | This process sets up the path constants, loads and registers
 | the autoloader, and loads the application environment.
 */

// 🔴 WAJIB: sesuaikan dengan NAMA FOLDER PROJECT CI4 kamu
$pathsConfig = FCPATH . '../my_kantin/app/Config/Paths.php';

// If the file does not exist, exit with error
if (! file_exists($pathsConfig)) {
    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo '❌ Error: app/Config/Paths.php tidak ditemukan.';
    exit(1);
}

require $pathsConfig;

// Load the framework bootstrap file
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'Boot.php';

// Run the application
exit(CodeIgniter\Boot::bootWeb($paths));
