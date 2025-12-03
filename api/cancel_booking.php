<?php
session_start();
header('Content-Type: application/json; charset=utf-8'); // JSON 응답 헤더

include '../inc/db_conn.php';

// JSON 데이터 받기
$json_str = file_get_contents('php://input');
$data = json_decode($json_str, true);

// 데이터 유효성 검사
if (!$data || !isset($data['booking_id'])) {
    echo json_encode(['status' => 'error', 'message' => '잘못된 요청 데이터입니다.']);
    exit;
}

$booking_id = $data['booking_id'];

// 로그인 체크
if (!isset($_SESSION['userid'])) {
    echo json_encode(['status' => 'error', 'message' => '로그인이 필요합니다.']);
    exit;
}

$userid = $_SESSION['userid'];

// 본인 예매인지 확인
// bookings 테이블과 users 테이블을 조인하여, 현재 로그인한 사람의 아이디와 일치하는지 확인
$check_sql = "SELECT b.id 
              FROM bookings b
              JOIN users u ON b.user_id = u.id
              WHERE b.id = '$booking_id' AND u.userid = '$userid'";

$check_res = mysqli_query($conn, $check_sql);

if (!$check_res) {
    echo json_encode(['status' => 'error', 'message' => 'DB 조회 오류: ' . mysqli_error($conn)]);
    exit;
}

if (mysqli_num_rows($check_res) > 0) {
    // 본인 것이 맞으면 삭제 실행
    $del_sql = "DELETE FROM bookings WHERE id = '$booking_id'";
    
    if (mysqli_query($conn, $del_sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => '삭제 실패: ' . mysqli_error($conn)]);
    }
} else {
    // 예매 내역이 없거나 다른 사람의 예매인 경우
    echo json_encode(['status' => 'error', 'message' => '예매 정보가 없거나 본인의 예매가 아닙니다.']);
}

mysqli_close($conn);
?>