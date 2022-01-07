<?php 
	$serverName = '127.0.0.1';
    $dbName   = 'ams';
    $username = 'root';
    $password = '';

    $dsn = "mysql:host=$serverName;dbname=$dbName";

    try {
        $connect = new PDO($dsn, $username, $password);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
?>