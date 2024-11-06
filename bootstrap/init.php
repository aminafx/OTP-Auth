<?php
require 'constants.php';
require 'config.php';
require BASE_PATH . 'libs/helpers.php';


try {
    $pdo = new PDO("mysql:host={$database_config->host};dbname={$database_config->dbname};charset={$database_config->charset}", $database_config->user, $database_config->password);
    // set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}