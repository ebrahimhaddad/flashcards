<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'put db name here');
define('DB_USER', 'put db user here');
define('DB_PASS', 'put db password here');

// mysqli connection, used by cards.php and practice.php
$db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$db) {
    error_log('mysqli connection failed: ' . mysqli_connect_error());
    die('Database connection error.');
}
mysqli_set_charset($db, 'utf8mb4');

// PDO connection, used by api_comment.php
try {
    // DSN (Data Source Name)
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    error_log('PDO connection failed: ' . $e->getMessage());
    die('Database connection error.');
}
