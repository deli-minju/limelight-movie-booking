<?php
// 한국 시간 설정
date_default_timezone_set('Asia/Seoul');

// JSON 응답 헤더
header('Content-Type: application/json; charset=utf-8');
// 불필요한 에러 출력 방지
error_reporting(0);

include '../inc/db_conn.php';

$type = isset($_GET['type']) ? $_GET['type'] : '';

// 영화 목록 가져오기
if ($type === 'movies') {
    $theater_id = $_GET['theater'];
    $date = $_GET['date'];
    
    $sql = "SELECT DISTINCT m.id, m.title, m.poster_img 
            FROM showtimes s 
            JOIN movies m ON s.movie_id = m.id 
            WHERE s.theater_id = '$theater_id' 
            AND DATE(s.start_time) = '$date'
            AND m.is_deleted = 0";
            
    $result = mysqli_query($conn, $sql);
    $data = [];
    while($row = mysqli_fetch_assoc($result)) $data[] = $row;
    echo json_encode($data);
    exit;
}

// 시간표 가져오기
if ($type === 'times') {
    $theater_id = $_GET['theater'];
    $date = $_GET['date'];
    $movie_id = $_GET['movie'];

    // movies 테이블을 조인해서 runtime(분)을 가져옴
    $sql = "SELECT s.id, s.start_time, s.screen_name, m.runtime 
            FROM showtimes s 
            JOIN movies m ON s.movie_id = m.id
            JOIN theaters t ON s.theater_id = t.id
            WHERE s.theater_id = '$theater_id' 
            AND s.movie_id = '$movie_id' 
            AND DATE(s.start_time) = '$date' 
            AND m.is_deleted = 0 
            AND t.is_deleted = 0
            ORDER BY s.start_time ASC";
            
    $result = mysqli_query($conn, $sql);
    $data = [];
    
    $now = time();

    while($row = mysqli_fetch_assoc($result)) {
        // 시작 시간
        $start_ts = strtotime($row['start_time']);
        
        // 종료 시간 계산
        $end_ts = $start_ts + ($row['runtime'] * 60);
        
        // 포맷팅 (H:i) -> 09:00
        $start_str = date("H:i", $start_ts);
        $end_str = date("H:i", $end_ts);
        
        // 현재 시간보다 시작 시간이 작으면 과거
        $row['is_past'] = ($start_ts < $now);

        $row['time_display'] = "$start_str ~ $end_str"; 
        
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
}
?>