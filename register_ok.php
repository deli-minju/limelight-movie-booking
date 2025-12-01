<?php
include 'inc/db_conn.php';

$userid = $_POST['userid'];
$password = $_POST['password'];
$name = $_POST['name'];
$nickname = $_POST['nickname'];
$email = $_POST['email'];

// 생년월일 합치기 (YYYY-MM-DD 형식)
$birth_year = $_POST['birth_year'];
$birth_month = str_pad($_POST['birth_month'], 2, "0", STR_PAD_LEFT);
$birth_day = str_pad($_POST['birth_day'], 2, "0", STR_PAD_LEFT);
$birth_date = $birth_year . "-" . $birth_month . "-" . $birth_day;

// 비밀번호 암호화
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 아이디 중복 체크
$check_sql = "SELECT * FROM users WHERE userid = '$userid' OR nickname = '$nickname'";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) > 0) {
    // 중복된 아이디나 닉네임이 있는 경우
    echo "<script>
        alert('이미 존재하는 아이디 또는 닉네임입니다.');
        history.back();
    </script>";
    exit;
}

// 회원가입 정보 DB 저장 (users 테이블)
// role 기본값은 'user', created_at은 자동 생성이므로 생략 가능
$sql = "INSERT INTO users (userid, password, username, nickname, email, birth_date) 
        VALUES ('$userid', '$hashed_password', '$name', '$nickname', '$email', '$birth_date')";

if (mysqli_query($conn, $sql)) {
    // 성공 시 웰컴 페이지로 이동
    echo "<script>
        location.href = 'welcome.php';
    </script>";
} else {
    // 실패 시 에러 메시지
    echo "<script>
        alert('회원가입에 실패했습니다. 관리자에게 문의하세요.\\n에러: " . mysqli_error($conn) . "');
        history.back();
    </script>";
}

mysqli_close($conn);
?>