<?php
ob_start();

session_start();
header('Content-Type: application/json; charset=utf-8');
error_reporting(0); // JSON 깨짐 방지 - 에러 메시지 숨김

include '../inc/db_conn.php';

ob_clean();

$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';

// 검색어가 없으면 빈 배열 반환
if ($keyword === '') {
    echo json_encode([]);
    exit;
}

$user_id = 0;
if (isset($_SESSION['userid'])) {
    $uid = $_SESSION['userid'];
    $u_sql = "SELECT id FROM users WHERE userid = '$uid'";
    $u_res = mysqli_query($conn, $u_sql);
    if ($u_row = mysqli_fetch_assoc($u_res)) {
        $user_id = $u_row['id'];
    }
}

// 띄어쓰기 제거 및 이스케이프
$search_nospace = str_replace(' ', '', $keyword);
$search_safe = mysqli_real_escape_string($conn, $search_nospace);

$sql = "SELECT m.id, m.title, m.poster_img, m.release_date, m.runtime, m.is_showing,
               GREATEST(DATEDIFF(m.release_date, CURDATE()), 0) as d_day,
               (SELECT COUNT(*) FROM wishlist w WHERE w.movie_id = m.id AND w.user_id = '$user_id') as is_liked,
               (SELECT COUNT(*) FROM reviews r WHERE r.movie_id = m.id) as review_count,
               (SELECT COUNT(*) FROM wishlist w2 WHERE w2.movie_id = m.id) as like_count
        FROM movies m
        WHERE REPLACE(m.title, ' ', '') LIKE '%$search_safe%' 
        AND m.is_deleted = 0
        ORDER BY m.release_date DESC";

$result = mysqli_query($conn, $sql);
$out = [];

if ($result) {
    while($row = mysqli_fetch_assoc($result)) {
        // boolean 변환
        $row['is_liked'] = ($row['is_liked'] > 0);
        $out[] = $row;
    }
}

echo json_encode($out);