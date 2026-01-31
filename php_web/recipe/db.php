<?php
$configPath = dirname(dirname(__DIR__ )). '/db_login.ini';
if (!file_exists($configPath)) {
    die("Configuration file not found.");
}
$dbSettings = parse_ini_file($configPath, true);

if (!$dbSettings) {
    die("Failed to parse configuration file.");
}
//
$dbHost = $dbSettings['database']['host'];
$dbName = $dbSettings['database']['db'];
$dbUser = $dbSettings['database']['username'];
$dbPass = $dbSettings['database']['password'];

?>