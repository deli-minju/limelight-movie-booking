<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);
include '../inc/db_conn.php';
include 'check_api_key.php'; // API Key 보안 검사

// POST 데이터 수신 - 보안 처리
$userid = mysqli_real_escape_string($conn, $_POST['userid']);
$password = $_POST['password']; // 비밀번호는 해싱할 거라 그냥 받음
$name = mysqli_real_escape_string($conn, $_POST['name']);
$nickname = mysqli_real_escape_string($conn, $_POST['nickname']);
$email = mysqli_real_escape_string($conn, $_POST['email']);

// 생년월일 처리
$birth_year = $_POST['birth_year'];
$birth_month = str_pad($_POST['birth_month'], 2, "0", STR_PAD_LEFT);
$birth_day = str_pad($_POST['birth_day'], 2, "0", STR_PAD_LEFT);
$birth_date = $birth_year . "-" . $birth_month . "-" . $birth_day;

// 필수 값 체크
if(!$userid || !$password || !$name || !$nickname || !$email || !$birth_year) {
    echo json_encode(['status' => 'error', 'message' => '모든 항목을 입력해주세요.']);
    exit;
}

// 비밀번호 암호화
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 중복 재확인
$check_sql = "SELECT id FROM users WHERE userid = '$userid' OR nickname = '$nickname'";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) > 0) {
    echo json_encode(['status' => 'error', 'message' => '이미 존재하는 아이디 또는 닉네임입니다.']);
    exit;
}

// DB 삽입
$sql = "INSERT INTO users (userid, password, username, nickname, email, birth_date) 
        VALUES ('$userid', '$hashed_password', '$name', '$nickname', '$email', '$birth_date')";

if (mysqli_query($conn, $sql)) {
    echo json_encode(['status' => 'success', 'message' => '회원가입 성공']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'DB 오류: ' . mysqli_error($conn)]);
}