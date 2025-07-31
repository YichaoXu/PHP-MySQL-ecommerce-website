<?php
// Error Reporting Turn On
ini_set('error_reporting', E_ALL);

// Setting up the time zone
date_default_timezone_set('Asia/Dubai');

// Host Name
$dbhost = getenv('DB_HOST') ?: 'db'; // 默认为'db'，即docker-compose里的服务名

// Database Name
$dbname = getenv('DB_NAME') ?: 'fashiony_ogs';

// Database Username
$dbuser = getenv('DB_USER') ?: 'root';

// Database Password
$dbpass = getenv('DB_PASS') ?: '';

// Defining base url
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
define("BASE_URL", $protocol . $host . "/");

// Getting Admin url
define("ADMIN_URL", BASE_URL . "admin" . "/");

try {
	$pdo = new PDO("mysql:host={$dbhost};dbname={$dbname}", $dbuser, $dbpass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch( PDOException $exception ) {
	echo "Connection error :" . $exception->getMessage();
}