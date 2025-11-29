<?php
include 'db_secrets.php';

$conn = mysqli_connect($db_host, $db_id, $db_pw, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "DB 연결 성공!"; // 배포 후엔 주석 처리하기
?>