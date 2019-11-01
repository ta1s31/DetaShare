<?php

$dsn = 'mysql:dbname=detashare;host=localhost;charset=utf8mb4';
$username = 'detashare';
$password = 'Hao123Hao123';
$driver_options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $driver_options);

}catch(PDOException $e) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
    exit($e->getMessage());
}

?>