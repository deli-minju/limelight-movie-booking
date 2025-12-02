<?php
session_start();
include 'inc/db_conn.php';

$userid = $_POST['userid'];
$password = $_POST['password'];

// SQL Injection 방지
$userid = mysqli_real_escape_string($conn, $userid);

$sql = "SELECT * FROM users WHERE userid = '$userid'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);
    $hash = $row['password'];

    // 비밀번호 검증 (사용자가 입력한 비번 vs DB의 암호화된 비번)
    if (password_verify($password, $hash)) {
        $_SESSION['userid'] = $row['userid'];
        $_SESSION['nickname'] = $row['nickname'];
        $_SESSION['role'] = $row['role'];
        
        echo "<script>
            location.href = 'index.php';
        </script>";       
    } else {
        echo "<script>
            location.href = 'login.php?error=invalid';
        </script>";
    }
} else {
    echo "<script>
        location.href = 'login.php?error=invalid';
    </script>";
}

mysqli_close($conn);
?>