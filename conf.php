<?php
include 'db-credentials.php';

// mysqli connection, used by cards.php and practice.php
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$conn) {
    error_log('mysqli connection failed: ' . mysqli_connect_error());
    die('Database connection error.');
}
mysqli_set_charset($conn, 'utf8mb4');
