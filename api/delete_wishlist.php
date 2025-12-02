<?php
session_start();
include '../inc/db_conn.php';

$data = json_decode(file_get_contents('php://input'), true);
$movie_id = $data['movie_id'];

if (!isset($_SESSION['userid'])) {
    echo json_encode(['status' => 'error', 'message' => '로그인이 필요합니다.']);
    exit;
}

$userid = $_SESSION['userid'];

$sql_u = "SELECT id FROM users WHERE userid = '$userid'";
$res_u = mysqli_query($conn, $sql_u);
$row_u = mysqli_fetch_assoc($res_u);
$user_id = $row_u['id'];

$del_sql = "DELETE FROM wishlist WHERE user_id = $user_id AND movie_id = $movie_id";

if (mysqli_query($conn, $del_sql)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
}
?>