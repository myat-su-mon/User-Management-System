<?php
    $host     = 'localhost';
    $port     = '5434';
    $db       = 'sec_db';
    $user     = 'signal';
    $password = 'bU7YGvBjyfD3';
    $dsn      = "";
    $pdo      = "";

    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$db;";
        $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    } catch (PDOException $e) {
        // echo $e->getMessage();
    }
?>
