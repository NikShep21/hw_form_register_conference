<?php

$config = require __DIR__ . '/config.php';

try {
    $pdo = new PDO(
        "mysql:host={$config['db_host']};port={$config['db_port']};dbname={$config['db_name']};charset=utf8mb4",
        $config['db_user'],
        $config['db_password']
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $error) {
    die('Ошибка подключения к базе данных: ' . $error->getMessage());
}
