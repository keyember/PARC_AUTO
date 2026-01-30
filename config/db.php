<?php

$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    die('Erreur : fichier .env non trouvé');
}

$env = parse_ini_file($envFile);

$host = $env['DB_HOST'] ?? throw new Exception('DB_HOST non défini');
$db   = $env['DB_NAME'] ?? throw new Exception('DB_NAME non défini');
$user = $env['DB_USER'] ?? throw new Exception('DB_USER non défini');
$pass = $env['DB_PASS'] ?? throw new Exception('DB_PASS non défini');
$charset = $env['DB_CHARSET'] ?? 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}
