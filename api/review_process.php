<?php
date_default_timezone_set('Asia/Seoul');
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);

include '../inc/db_conn.php';
session_start();

$method = $_SERVER['REQUEST_METHOD'];

// 리뷰 목록 가져오기
if ($method === 'GET' && isset($_GET['mode']) && $_GET['mode'] === 'list') {
    $movie_id = $_GET['movie_id'];

    $sql = "SELECT r.content, r.created_at, u.nickname 
            FROM reviews r 
            JOIN users u ON r.user_id = u.id 
            WHERE r.movie_id = '$movie_id' 
            ORDER BY r.id DESC"; // 최신순
            
    $result = mysqli_query($conn, $sql);
    $data = [];
    
    while($row = mysqli_fetch_assoc($result)) {
        $row['created_at'] = date('Y.m.d', strtotime($row['created_at']));
        $data[] = $row;
    }
    
    echo json_encode($data);
    exit;
}

// 리뷰 저장하기
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($_SESSION['userid'])) {
        echo json_encode(['status' => 'not_logged_in']);
        exit;
    }
    
    $userid = $_SESSION['userid'];
    $movie_id = $input['movie_id'];
    $content = mysqli_real_escape_string($conn, $input['content']);

    // 사용자 DB ID 찾기
    $sql_u = "SELECT id FROM users WHERE userid = '$userid'";
    $res_u = mysqli_query($conn, $sql_u);
    $row_u = mysqli_fetch_assoc($res_u);
    $user_db_id = $row_u['id'];
    
    $sql = "INSERT INTO reviews (user_id, movie_id, content) VALUES ('$user_db_id', '$movie_id', '$content')";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
    exit;
}
?>