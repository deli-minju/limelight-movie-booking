<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 한국 시간 설정
date_default_timezone_set('Asia/Seoul');

// 로그인 체크
if (!isset($_SESSION['userid'])) {
    echo "<script>
        alert('로그인이 필요한 서비스입니다.');
        location.href = 'login.php';
    </script>";
    exit;
}

include 'inc/db_conn.php';

$userid = $_SESSION['userid'];
$sql_u = "SELECT id FROM users WHERE userid = '$userid'";
$res_u = mysqli_query($conn, $sql_u);
$row_u = mysqli_fetch_assoc($res_u);
$user_db_id = $row_u['id'];

// 예매 내역 조회 + 종료 시간 계산
$sql = "SELECT b.id as booking_id, b.total_price, b.adult_count, b.teen_count, b.senior_count, b.booking_date,
               m.title, m.poster_img, m.runtime,
               t.name as theater_name,
               s.start_time, s.screen_name,
               DATE_ADD(s.start_time, INTERVAL m.runtime MINUTE) as end_time
        FROM bookings b
        JOIN showtimes s ON b.showtime_id = s.id
        JOIN movies m ON s.movie_id = m.id
        JOIN theaters t ON s.theater_id = t.id
        WHERE b.user_id = '$user_db_id'
        ORDER BY s.start_time DESC"; // 최신 상영일 순으로 정렬

$result = mysqli_query($conn, $sql);

$current_time = time();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>예매내역 - LimeLight</title>
    
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/reservation.css">
    <link rel="stylesheet" href="css/booking_list.css">
</head>
<body>

    <div class="layout-wrapper">
        <?php include 'inc/sidebar.php'; ?>

        <main class="main-content reservation-bg">
            <div class="reservation-header">
                <div class="tab-menu">
                    <a href="reservation.php" class="tab-item">극장별 예매</a>
                    <a href="booking_list.php" class="tab-item active">예매내역</a>
                </div>
            </div>

            <div class="booking-list-container">
                
                <h2 class="page-sub-title">나의 예매 내역</h2>

                <div class="history-list">
                    <?php 
                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)): 
                            $start_dt = strtotime($row['start_time']);
                            $end_dt = strtotime($row['end_time']);

                            $date_str = date("Y.m.d", $start_dt);
                            $start_time_str = date("H:i", $start_dt);
                            $end_time_str = date("H:i", $end_dt); // 종료 시간

                            $yoil_arr = ["일","월","화","수","목","금","토"];
                            $yoil = $yoil_arr[date("w", $start_dt)];
                            
                            // 2024.12.04 (수) 14:00 ~ 16:00
                            $full_date = "$date_str ($yoil) $start_time_str ~ $end_time_str";
                            
                            // 인원 문자열 (0명인 항목은 표시 X)
                            $people_str = [];
                            if($row['adult_count'] > 0) $people_str[] = "일반 " . $row['adult_count'];
                            if($row['teen_count'] > 0) $people_str[] = "청소년 " . $row['teen_count'];
                            if($row['senior_count'] > 0) $people_str[] = "경로/우대 " . $row['senior_count'];
                            $people_text = implode(", ", $people_str);

                            // 현재 시간이 상영 시작 시간보다 이전이어야 취소 가능
                            $is_cancelable = ($current_time < $start_dt);
                    ?>

                        <div class="ticket-card">
                            <div class="poster-area">
                                <img src="<?= $row['poster_img'] ?>" alt="poster">
                            </div>
                            
                            <div class="info-area">
                                <div class="ticket-header">
                                    <span class="booking-date">예매일: <?= date("Y.m.d", strtotime($row['booking_date'])) ?></span>
                                </div>
                                <h3 class="movie-title"><?= $row['title'] ?></h3>
                                <div class="detail-info">
                                    <p><strong>극장</strong> <?= $row['theater_name'] ?> / <?= $row['screen_name'] ?></p>
                                    <p><strong>일시</strong> <span class="highlight-time"><?= $full_date ?></span></p>
                                    <p><strong>인원</strong> <?= $people_text ?></p>
                                </div>
                            </div>
                            
                            <div class="action-area">
                                <span class="total-price"><?= number_format($row['total_price']) ?>원</span>

                                <?php if ($is_cancelable): ?>
                                    <button class="btn-cancel" onclick="cancelBooking(<?= $row['booking_id'] ?>)">예매 취소</button>
                                <?php else: ?>
                                    <span style="font-size: 13px; color: #777; margin-top: 10px; font-weight: 500;">상영 종료/취소 불가</span>
                                <?php endif; ?>

                            </div>
                        </div>
                    <?php 
                        endwhile; 
                    } else { 
                    ?>
                        <div class="no-history">
                            <p>예매 내역이 없습니다.</p>
                            <a href="reservation.php" class="btn-go-reserve">영화 예매하러 가기</a>
                        </div>
                    <?php } ?>
                </div>

            </div>

        </main>
    </div>

    <script src="js/booking_list.js"></script>

</body>
</html>