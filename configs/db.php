<?php

$dsn = 'mysql:host=;dbname=;charset=utf8';
$username = '';
$password = '';

try {
    $conn = new PDO(dsn: $dsn, username: $username, password: $password);
    $conn->setAttribute(attribute: PDO::ATTR_ERRMODE, value: PDO::ERRMODE_EXCEPTION);
    $conn->exec(statement: "SET NAMES utf8");
} catch (PDOException $e) {
    echo 'Connection Failed: ' . $e->getMessage();
}