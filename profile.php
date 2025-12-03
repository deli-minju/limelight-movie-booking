<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 한국 시간 설정
date_default_timezone_set('Asia/Seoul');

if (!isset($_SESSION['userid'])) {
    echo "<script>
        alert('로그인이 필요한 서비스입니다.');
        location.href = 'login.php';
    </script>";
    exit;
}

include 'inc/db_conn.php';
include_once 'inc/icons.php'; 

$userid = $_SESSION['userid'];

// 사용자 정보 조회
$sql_user = "SELECT * FROM users WHERE userid = '$userid'";
$result_user = mysqli_query($conn, $sql_user);
$row_user = mysqli_fetch_assoc($result_user);
$user_db_id = $row_user['id'];

// D-Day 계산
$join_date = new DateTime($row_user['created_at']);
$today = new DateTime();
$interval = $join_date->diff($today);
$d_day = $interval->days + 1;

// 레벨 산정용 관람 완료 횟수 조회
$sql_level = "SELECT COUNT(*) as cnt 
              FROM bookings b
              JOIN showtimes s ON b.showtime_id = s.id
              JOIN movies m ON s.movie_id = m.id
              WHERE b.user_id = '$user_db_id'
              AND DATE_ADD(s.start_time, INTERVAL m.runtime MINUTE) < NOW()";

$result_level = mysqli_query($conn, $sql_level);
$row_level = mysqli_fetch_assoc($result_level);
$booking_count = $row_level['cnt'];

// 레벨 결정 로직
$level_num = 1;
$level_name = "뉴비";
$level_title = "설레는 첫 티켓";
$level_desc = "LimeLight와 함께하는 영화 여행의 첫 번째 장면이 시작되었습니다.";
$next_goal = 6; 

if ($booking_count >= 21) {
    $level_num = 4;
    $level_name = "VIP";
    $level_title = "영화관이 내 집 안방";
    $level_desc = "이 정도면 눈 감고도 상영관 찾아가시겠어요.";
    $next_goal = 0; 
} elseif ($booking_count >= 13) {
    $level_num = 3;
    $level_name = "씨네필";
    $level_title = "불 켜져야 일어나는 편";
    $level_desc = "엔딩 크레딧의 마지막 한 줄까지, 영화의 여운을 놓치지 않는 분이시군요.";
    $next_goal = 21;
} elseif ($booking_count >= 6) {
    $level_num = 2;
    $level_name = "매니아";
    $level_title = "명당자리 콜렉터";
    $level_desc = "어느 상영관이든 최적의 몰입감을 주는 자리를 알고 계시는군요.";
    $next_goal = 13;
}

// 관람 통계 조회
$sql_stats = "SELECT 
                SUM(m.runtime) as total_minutes,
                COUNT(DISTINCT m.id) as unique_movies
              FROM bookings b
              JOIN showtimes s ON b.showtime_id = s.id
              JOIN movies m ON s.movie_id = m.id
              WHERE b.user_id = '$user_db_id' 
              AND DATE_ADD(s.start_time, INTERVAL m.runtime MINUTE) < NOW()";

$result_stats = mysqli_query($conn, $sql_stats);
$row_stats = mysqli_fetch_assoc($result_stats);

$total_minutes = $row_stats['total_minutes'] ? $row_stats['total_minutes'] : 0;
$unique_movies = $row_stats['unique_movies'] ? $row_stats['unique_movies'] : 0;

$hours = floor($total_minutes / 60);
$minutes = $total_minutes % 60;
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>프로필 - LimeLight</title>
    
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>

    <div class="layout-wrapper">
        <?php include 'inc/sidebar.php'; ?>

        <main class="main-content profile-bg">
            <div class="profile-container">              
                <div class="level-header-section">
                    <div class="level-badge">Level <?= $level_num ?>. <?= $level_name ?></div>
                    <h2 class="level-title">"<?= $level_title ?>"</h2>
                    <p class="level-desc"><?= $level_desc ?></p>
                </div>

                <div class="user-info-section">
                    <form id="profile-form" enctype="multipart/form-data">
                        <div class="profile-img-wrapper">
                            <div class="profile-img-large" id="preview-img" 
                                 style="background-image: url('<?= $row_user['profile_img'] ?: 'img/profile-default.png' ?>');">
                            </div>
                            <input type="file" name="profile_image" id="file-input" accept="image/*" style="display: none;" onchange="uploadProfileImage()">
                            <button type="button" class="btn-change-img" onclick="document.getElementById('file-input').click()" title="프로필 사진 변경">
                                <?= getIconCamera() ?>
                            </button>
                        </div>
                    </form>

                    <p class="d-day-text">
                        <span class="highlight-text"><?= $row_user['nickname'] ?></span>님의 일상을 비춘 지 
                        <span class="highlight-lime">'<?= $d_day ?>일'</span>이 지났어요.
                    </p>
                </div>

                <div class="stats-section">
                    <div class="stat-item">
                        <p>인생의 <span class="highlight-lime">'<?= $hours ?>시간 <?= $minutes ?>분'</span>을 영화로 채우셨네요.</p>
                    </div>
                    <div class="stat-item">
                        <p>지금까지 <span class="highlight-lime">'<?= $unique_movies ?>편'</span>의 이야기를 수집했습니다.</p>
                    </div>
                </div>

                <!-- 레벨 프로그레스 바 -->
                <?php if ($next_goal > 0): 
                    $prev_goal = 0;
                    if ($level_num == 2) $prev_goal = 5;
                    if ($level_num == 3) $prev_goal = 12;
                    
                    $range = $next_goal - $prev_goal;
                    $current_in_range = $booking_count - $prev_goal;
                    if ($current_in_range < 0) $current_in_range = 0;
                    
                    $percent = ($current_in_range / $range) * 100;
                    if($percent > 100) $percent = 100;
                ?>
                <div class="exp-bar-container">
                    <div class="exp-info">
                        <span>Lv.<?= $level_num ?></span>
                        <span>Lv.<?= $level_num + 1 ?></span>
                    </div>
                    <div class="exp-track">
                        <div class="exp-fill" style="width: <?= $percent ?>%;"></div>
                    </div>
                    <p class="exp-msg">
                        <span class="highlight-lime"><?= $next_goal - $booking_count ?>편</span>의 영화를 더 보면 레벨업해요!
                    </p>
                </div>
                <?php else: ?>
                <div class="exp-bar-container">
                    <div class="exp-track">
                        <div class="exp-fill" style="width: 100%;"></div>
                    </div>
                    <p class="exp-msg">최고 레벨을 달성하셨습니다! 👑</p>
                </div>
                <?php endif; ?>

            </div>

        </main>
    </div>

    <script>
        function uploadProfileImage() {
            const fileInput = document.getElementById('file-input');
            const file = fileInput.files[0];

            if (!file) return;

            if (!file.type.startsWith('image/')) {
                alert('이미지 파일만 업로드 가능합니다.');
                return;
            }

            const formData = new FormData();
            formData.append('profile_image', file);

            fetch('api/upload_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('프로필 사진이 변경되었습니다.');
                    location.reload();
                } else {
                    alert('변경 실패: ' + (data.message || '알 수 없는 오류'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('서버 통신 오류가 발생했습니다.');
            });
        }
    </script>

</body>
</html>