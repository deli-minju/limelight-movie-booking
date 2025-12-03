<?php
session_start();
include '../inc/db_conn.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($_SESSION['userid'])) {
    echo json_encode(['status' => 'error', 'message' => '로그인 필요']);
    exit;
}

// 사용자 ID 조회
$userid = $_SESSION['userid'];
$sql_u = "SELECT id FROM users WHERE userid = '$userid'";
$res_u = mysqli_query($conn, $sql_u);
$row_u = mysqli_fetch_assoc($res_u);
$user_db_id = $row_u['id'];

// 데이터 준비
$showtime_id = $data['showtime_id'];
$adult = $data['adult'];
$teen = $data['teen'];
$senior = $data['senior'];
$total_price = $data['total_price'];

// 예매 정보 저장
$sql = "INSERT INTO bookings (user_id, showtime_id, adult_count, teen_count, senior_count, total_price)
        VALUES ('$user_db_id', '$showtime_id', '$adult', '$teen', '$senior', '$total_price')";

if (mysqli_query($conn, $sql)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
}
?>