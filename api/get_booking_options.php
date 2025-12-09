<?php
// 한국 시간 설정
date_default_timezone_set('Asia/Seoul');
header('Content-Type: application/json; charset=utf-8');
error_reporting(0); // JSON 파싱 에러 방지

include '../inc/db_conn.php';

// type 파라미터 확인
$type = isset($_GET['type']) ? $_GET['type'] : '';

// 극장 목록 가져오기
if ($type === 'theaters') {
    $sql = "SELECT id, name, location FROM theaters WHERE is_deleted = 0 ORDER BY id ASC";
    $result = mysqli_query($conn, $sql);
    
    $data = [];
    if ($result) {
        while($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    echo json_encode($data);
    exit;
}

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
    if ($result) {
        while($row = mysqli_fetch_assoc($result)) $data[] = $row;
    }
    echo json_encode($data);
    exit;
}

// 시간표 가져오기
if ($type === 'times') {
    $theater_id = $_GET['theater'];
    $date = $_GET['date'];
    $movie_id = $_GET['movie'];

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

    if ($result) {
        while($row = mysqli_fetch_assoc($result)) {
            $start_ts = strtotime($row['start_time']);
            $end_ts = $start_ts + ($row['runtime'] * 60);
            
            $start_str = date("H:i", $start_ts);
            $end_str = date("H:i", $end_ts);
            
            $row['is_past'] = ($start_ts < $now);
            $row['time_display'] = "$start_str ~ $end_str"; 
            
            $data[] = $row;
        }
    }
    echo json_encode($data);
    exit;
}

// type이 안 맞을 경우 빈 배열
echo json_encode([]);