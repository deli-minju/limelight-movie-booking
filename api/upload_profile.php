<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);

include '../inc/db_conn.php';

if (!isset($_SESSION['userid'])) {
    echo json_encode(['status' => 'error', 'message' => '로그인이 필요합니다.']);
    exit;
}

$userid = $_SESSION['userid'];

// 파일 업로드 확인
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    
    $fileTmpPath = $_FILES['profile_image']['tmp_name'];
    $fileName = $_FILES['profile_image']['name'];
    $fileSize = $_FILES['profile_image']['size'];
    $fileType = $_FILES['profile_image']['type'];

    // 확장자 추출 및 검사
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));
    $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'webp');

    if (in_array($fileExtension, $allowedfileExtensions)) {
        
        // 저장할 폴더 생성
        $uploadFileDir = '../uploads/profile/';
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true);
        }

        // 파일명 중복 방지
        $newFileName = $userid . '_' . time() . '.' . $fileExtension;
        $dest_path = $uploadFileDir . $newFileName;
        
        // DB에 저장할 경로
        $db_path = 'uploads/profile/' . $newFileName;

        // 파일 이동
        if(move_uploaded_file($fileTmpPath, $dest_path)) {
            
            // DB 업데이트
            $sql = "UPDATE users SET profile_img = '$db_path' WHERE userid = '$userid'";
            
            if(mysqli_query($conn, $sql)) {
                echo json_encode(['status' => 'success', 'path' => $db_path]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'DB 업데이트 실패']);
            }

        } else {
            echo json_encode(['status' => 'error', 'message' => '파일 이동 실패']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => '허용되지 않는 파일 형식입니다.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => '파일 전송 중 오류가 발생했습니다.']);
}

mysqli_close($conn);
?>