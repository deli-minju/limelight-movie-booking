<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);
include '../inc/db_conn.php';

$type = $_GET['type'] ?? ''; // 'userid' or 'nickname'
$value = $_GET['value'] ?? '';

if (!$type || !$value) {
    echo json_encode(['status' => 'error', 'message' => '파라미터 오류']);
    exit;
}

$safe_value = mysqli_real_escape_string($conn, $value);
$sql = "";

if ($type === 'userid') {
    $sql = "SELECT id FROM users WHERE userid = '$safe_value'";
} elseif ($type === 'nickname') {
    $sql = "SELECT id FROM users WHERE nickname = '$safe_value'";
}

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo json_encode(['status' => 'duplicate']);
} else {
    echo json_encode(['status' => 'available']);
}