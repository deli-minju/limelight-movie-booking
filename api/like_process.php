<?php
session_start();
include '../inc/db_conn.php';

$data = json_decode(file_get_contents('php://input'), true);
$movie_id = $data['movie_id'];

if (!isset($_SESSION['userid'])) {
    echo json_encode(['status' => 'not_logged_in']);
    exit;
}

$userid = $_SESSION['userid'];

$sql_u = "SELECT id FROM users WHERE userid = '$userid'";
$res_u = mysqli_query($conn, $sql_u);
$row_u = mysqli_fetch_assoc($res_u);
$user_id = $row_u['id'];

$check_sql = "SELECT * FROM wishlist WHERE user_id = $user_id AND movie_id = $movie_id";
$check_res = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_res) > 0) {
    $del_sql = "DELETE FROM wishlist WHERE user_id = $user_id AND movie_id = $movie_id";
    mysqli_query($conn, $del_sql);
    echo json_encode(['status' => 'success', 'action' => 'unliked']);
} else {
    $ins_sql = "INSERT INTO wishlist (user_id, movie_id) VALUES ($user_id, $movie_id)";
    mysqli_query($conn, $ins_sql);
    echo json_encode(['status' => 'success', 'action' => 'liked']);
}