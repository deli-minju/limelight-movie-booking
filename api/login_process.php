<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
// 에러가 발생해도 HTML이 출력되지 않도록 설정
error_reporting(0); 

include '../inc/db_conn.php';

// POST 데이터 받기
$userid = isset($_POST['userid']) ? $_POST['userid'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (!$userid || !$password) {
    echo json_encode(['status' => 'error', 'message' => '아이디와 비밀번호를 입력해주세요.']);
    exit;
}

// 사용자 조회
$sql = "SELECT * FROM users WHERE userid = '$userid'";
$result = mysqli_query($conn, $sql);

if ($row = mysqli_fetch_assoc($result)) {
    // 사용자의 DB 비밀번호는 암호화된 해시
    if (password_verify($password, $row['password'])) {
        // 로그인 성공: 세션 생성
        $_SESSION['userid'] = $row['userid'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        echo json_encode(['status' => 'success', 'message' => '로그인 성공']);
    } else {
        echo json_encode(['status' => 'error', 'message' => '비밀번호가 일치하지 않습니다.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => '존재하지 않는 아이디입니다.']);
}
?>