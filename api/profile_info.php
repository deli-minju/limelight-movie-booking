<?php
session_start();
date_default_timezone_set('Asia/Seoul');
header('Content-Type: application/json; charset=utf-8');
include '../inc/db_conn.php';

if (!isset($_SESSION['userid'])) { echo json_encode(['status'=>'not_logged_in']); exit; }
$userid = $_SESSION['userid'];

// 사용자 기본 정보
$sql_user = "SELECT id, nickname, email, profile_img, created_at FROM users WHERE userid = '$userid'";
$res_user = mysqli_query($conn, $sql_user);
if (!$res_user || !($row_user = mysqli_fetch_assoc($res_user))) {
    echo json_encode(['status'=>'error','message'=>'user not found']); exit;
}
$user_db_id = $row_user['id'];

// D-Day 계산
$join_dt = new DateTime($row_user['created_at']);
$today   = new DateTime('today');
$d_day   = $join_dt->diff($today)->days + 1; // 가입일 포함

// 관람 완료 횟수
$sql_level = "SELECT COUNT(*) as cnt
              FROM bookings b
              JOIN showtimes s ON b.showtime_id = s.id
              JOIN movies m ON s.movie_id = m.id
              WHERE b.user_id = '$user_db_id'
              AND DATE_ADD(s.start_time, INTERVAL m.runtime MINUTE) < NOW()";
$row_level = mysqli_fetch_assoc(mysqli_query($conn, $sql_level));
$booking_count = $row_level['cnt'] ?? 0;

// 레벨
$level_num=1; $level_name="뉴비"; $level_title="설레는 첫 티켓";
$level_desc="LimeLight와 함께하는 영화 여행의 첫 번째 장면이 시작되었습니다."; $next_goal=6;
if ($booking_count >= 21){ $level_num=4; $level_name="VIP"; $level_title="영화관이 내 집 안방";
  $level_desc="이 정도면 눈 감고도 상영관 찾아가시겠어요."; $next_goal=0; }
elseif ($booking_count >= 13){ $level_num=3; $level_name="시네필"; $level_title="불 켜져야 일어나는 편";
  $level_desc="엔딩 크레딧의 마지막 한 줄까지, 영화의 여운을 놓치지 않는 분이시군요."; $next_goal=21; }
elseif ($booking_count >= 6){ $level_num=2; $level_name="마니아"; $level_title="명당자리 콜렉터";
  $level_desc="어느 상영관이든 최적의 몰입감을 주는 자리를 알고 계시는군요."; $next_goal=13; }

// 관람 통계
$sql_stats = "SELECT SUM(m.runtime) as total_minutes, COUNT(DISTINCT m.id) as unique_movies
              FROM bookings b
              JOIN showtimes s ON b.showtime_id = s.id
              JOIN movies m ON s.movie_id = m.id
              WHERE b.user_id = '$user_db_id'
              AND DATE_ADD(s.start_time, INTERVAL m.runtime MINUTE) < NOW()";
$row_stats = mysqli_fetch_assoc(mysqli_query($conn, $sql_stats));
$total_minutes = $row_stats['total_minutes'] ?? 0;
$unique_movies = $row_stats['unique_movies'] ?? 0;

// 진행률 계산
if ($next_goal > 0) {
    $prev_goal = ($level_num==2)?5:(($level_num==3)?12:0);
    $range = $next_goal - $prev_goal;
    $current_in_range = max(0, $booking_count - $prev_goal);
    $percent = min(100, ($range>0 ? $current_in_range / $range * 100 : 100));
} else { $percent = 100; }

echo json_encode([
  'status'=>'success',
  'data'=>[
    'nickname'=>$row_user['nickname'],
    'email'=>$row_user['email'],
    'profile_img'=>$row_user['profile_img'],
    'created_at'=>$row_user['created_at'],
    'd_day'=>$d_day,
    'level_num'=>$level_num,
    'level_name'=>$level_name,
    'level_title'=>$level_title,
    'level_desc'=>$level_desc,
    'next_goal'=>$next_goal,
    'booking_count'=>$booking_count,
    'percent'=>$percent,
    'total_minutes'=>$total_minutes,
    'unique_movies'=>$unique_movies
  ]
]);
