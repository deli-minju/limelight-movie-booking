<?php
// 안드로이드 MovieRepository.java에 적은 키와 동일
$my_secret_key = "limelight_secret_1234"; 

// 앱이 POST로 보낸 api_key를 받음
$app_key = $_POST['api_key'] ?? '';

if ($my_secret_key !== $app_key) {
    // 키가 틀리면 JSON 에러를 뱉고 강제 종료
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'error', 'message' => '허용되지 않은 접근입니다. (API Key Error)']);
    exit;
}