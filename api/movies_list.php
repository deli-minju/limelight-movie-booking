<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);
session_start();

include '../inc/db_conn.php';

$scope = $_GET['scope'] ?? 'now';
$order = $_GET['order'] ?? 'popular_desc'; // 기본값 - 좋아요순

$user_id = 0;
if (isset($_SESSION['userid'])) {
    $uid = $_SESSION['userid'];
    $u_sql = "SELECT id FROM users WHERE userid = '$uid'";
    $u_res = mysqli_query($conn, $u_sql);
    if ($u_row = mysqli_fetch_assoc($u_res)) $user_id = $u_row['id'];
}

$baseSql = "SELECT m.id, m.title, m.poster_img, m.release_date, m.runtime, m.is_showing,
                   GREATEST(DATEDIFF(m.release_date, CURDATE()),0) AS d_day,
                   COUNT(w.id) AS like_count,
                   (SELECT COUNT(*) FROM wishlist w2 WHERE w2.movie_id = m.id AND w2.user_id = '$user_id') as is_liked,
                   (SELECT COUNT(*) FROM reviews r WHERE r.movie_id = m.id) as review_count
            FROM movies m
            LEFT JOIN wishlist w ON m.id = w.movie_id
            WHERE m.is_deleted = 0";

if ($scope === 'now') {
    $baseSql .= " AND m.release_date <= CURDATE() AND m.is_showing = 1";
} elseif ($scope === 'soon') {
    $baseSql .= " AND m.release_date > CURDATE()";
} elseif ($scope === 'mylist') {
    if ($user_id == 0) { echo json_encode([]); exit; }
}

// 정렬
// 값이 같을 경우: 최신 개봉일 -> 제목 가나다 순으로 처리
$orderSql = "";

if ($order === 'review_desc') {
    // 한줄평 많은 순 -> 개봉일 최신 -> 제목
    $orderSql = " ORDER BY review_count DESC, m.release_date DESC, m.title ASC";
} 
elseif ($order === 'popular_desc') {
    // 좋아요 많은 순 -> 개봉일 최신 -> 제목
    $orderSql = " ORDER BY like_count DESC, m.release_date DESC, m.title ASC";
} 
elseif ($order === 'title_asc') {
    // 가나다 순 -> 개봉일 최신
    $orderSql = " ORDER BY m.title ASC, m.release_date DESC";
} 
elseif ($order === 'release_asc') {
    // 개봉일 빠른 순 -> 제목 가나다
    $orderSql = " ORDER BY m.release_date ASC, m.title ASC";
} 
elseif ($order === 'release_desc') {
    // 개봉일 늦은 순 -> 제목 가나다
    $orderSql = " ORDER BY m.release_date DESC, m.title ASC";
} 
else {
    // 기본 안전장치
    $orderSql = " ORDER BY m.release_date DESC";
}

if ($scope === 'mylist') {
    $sql = $baseSql . " GROUP BY m.id HAVING is_liked > 0" . $orderSql;
} else {
    $sql = $baseSql . " GROUP BY m.id" . $orderSql;
}

$res = mysqli_query($conn, $sql);
$out = [];
if ($res) {
    while($row = mysqli_fetch_assoc($res)) {
        $row['is_liked'] = ($row['is_liked'] > 0);
        $out[] = $row;
    }
}
echo json_encode($out);