<?php
include_once 'inc/db_secrets.php';

$conn = mysqli_connect($db_host, $db_id, $db_pw, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");