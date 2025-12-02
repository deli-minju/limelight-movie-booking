<?php
include 'inc/db_conn.php';

// GET 요청값 받기
$type = isset($_GET['type']) ? $_GET['type'] : '';
$value = isset($_GET['value']) ? $_GET['value'] : '';

// 값이 없으면 에러
if (empty($type) || empty($value)) {
    echo json_encode(['status' => 'error', 'message' => '입력값이 없습니다.']);
    exit;
}

// SQL Injection 방지
$value = mysqli_real_escape_string($conn, $value);

// 쿼리 실행
if ($type == 'userid') {
    $sql = "SELECT count(*) as cnt FROM users WHERE userid = '$value'";
} elseif ($type == 'nickname') {
    $sql = "SELECT count(*) as cnt FROM users WHERE nickname = '$value'";
} else {
    echo json_encode(['status' => 'error', 'message' => '잘못된 요청입니다.']);
    exit;
}

$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

// cnt가 0보다 크면 중복, 0이면 사용가능
if ($row['cnt'] > 0) {
    echo json_encode(['status' => 'duplicate']);
} else {
    echo json_encode(['status' => 'available']);
}

mysqli_close($conn);