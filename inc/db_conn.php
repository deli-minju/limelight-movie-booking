<?php
include_once __DIR__ . '/db_secrets.php';

$conn = mysqli_connect($db_host, $db_id, $db_pw, $db_name);

if (!$conn) {
    die(json_encode(['status' => 'error', 'message' => 'DB Connection Failed']));
}

mysqli_set_charset($conn, "utf8mb4");