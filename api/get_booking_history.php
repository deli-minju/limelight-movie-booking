<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
// 에러 메시지가 JSON 응답을 깨지 않도록 숨김
error_reporting(0); 

include '../inc/db_conn.php';

// 로그인 체크
if (!isset($_SESSION['userid'])) { 
    echo json_encode([]); 
    exit; 
}

$userid = $_SESSION['userid'];

// 유저 DB ID 조회
$sql_u = "SELECT id FROM users WHERE userid = '$userid'";
$res_u = mysqli_query($conn, $sql_u);
if (!$res_u || !($row_u = mysqli_fetch_assoc($res_u))) { 
    echo json_encode([]); 
    exit; 
}
$user_db_id = $row_u['id'];

// theaters 테이블을 조인하여 branch_name을 가져오고
// start_time + runtime을 계산하여 end_time 만들기
$sql = "SELECT b.id as booking_id, b.total_price,
               m.title, m.poster_img,
               t.name as branch_name, s.screen_name, 
               s.start_time,
               DATE_ADD(s.start_time, INTERVAL m.runtime MINUTE) as end_time
        FROM bookings b
        JOIN showtimes s ON b.showtime_id = s.id
        JOIN movies m ON s.movie_id = m.id
        JOIN theaters t ON s.theater_id = t.id
        WHERE b.user_id = '$user_db_id'
        ORDER BY s.start_time DESC";

$res = mysqli_query($conn, $sql);
$out = [];

if ($res) {
    while($row = mysqli_fetch_assoc($res)) {
        $out[] = $row;
    }
}

echo json_encode($out);