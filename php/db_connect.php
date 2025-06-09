<?php
// File: php/db_connect.php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');    // Ganti jika berbeda
define('DB_PASSWORD', '');        // Ganti jika berbeda
define('DB_NAME', 'lab_inventory_db');

require_once dirname(__DIR__) . '/vendor/autoload.php';

$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");
?>