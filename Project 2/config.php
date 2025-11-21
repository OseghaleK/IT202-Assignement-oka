<?php
session_start();
$DB_HOST = "sql1.njit.edu";
$DB_NAME = "oka";
$DB_USER = "oka";
$DB_PASS = '$Helenakh2025';

$dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    die("Database connection failed.");
}
?>
