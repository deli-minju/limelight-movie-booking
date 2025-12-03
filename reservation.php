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

// 극장 목록 미리 가져오기
$sql_theater = "SELECT * FROM theaters ORDER BY name ASC";
$result_theater = mysqli_query($conn, $sql_theater);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>예매 - LimeLight</title>
    
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/reservation.css"> 
</head>
<body>
    <div class="layout-wrapper">
        <?php include 'inc/sidebar.php'; ?>

        <main class="main-content reservation-bg">
            <div class="reservation-header">
                <div class="tab-menu">
                    <a href="reservation.php" class="tab-item active">극장별 예매</a>
                    <a href="booking_list.php" class="tab-item">예매내역</a>
                </div>
            </div>

            <div class="booking-container">
                <div class="step-section">
                    <h3 class="step-title">극장 선택</h3>
                    <div class="horizontal-scroll-box" id="theater-list">
                        <?php while($row = mysqli_fetch_assoc($result_theater)): ?>
                            <button class="select-btn theater-btn" onclick="selectTheater(this, <?= $row['id'] ?>)">
                                <?= $row['name'] ?>
                            </button>
                        <?php endwhile; ?>
                    </div>
                </div>

                <div class="step-section">
                    <h3 class="step-title">날짜 선택</h3>
                    <div class="horizontal-scroll-box" id="date-list">
                        <?php 
                        $week = array("일", "월", "화", "수", "목", "금", "토");
                        for($i=0; $i<7; $i++): 
                            $timestamp = strtotime("+$i days");
                            $date_val = date("Y-m-d", $timestamp);
                            $day_val = date("d", $timestamp);
                            $yoil = $week[date("w", $timestamp)];
                        ?>
                            <button class="select-btn date-btn" onclick="selectDate(this, '<?= $date_val ?>')">
                                <span class="day-num"><?= $day_val ?></span>
                                <span class="day-yoil"><?= $yoil ?></span>
                            </button>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="step-section">
                    <h3 class="step-title">영화 선택</h3>
                    <div class="horizontal-scroll-box" id="movie-list">
                        <div class="empty-msg">극장과 날짜를 먼저 선택해주세요.</div>
                    </div>
                </div>

                <div class="step-section">
                    <h3 class="step-title">
                        시간 선택 
                        <span class="jojo-info">※ 조조(07:00~11:00) 4,000원 할인</span>
                    </h3>
                    <div class="horizontal-scroll-box" id="time-list">
                        <div class="empty-msg">영화를 선택해주세요.</div>
                    </div>
                </div>

                <div class="step-section count-payment-section">
                    <div class="count-box">
                        <h3 class="step-title">
                            인원 선택
                            <span class="sub-info">(최대 8명)</span>
                        </h3>
                        <div class="people-inputs">
                            <div class="p-group">
                                <label>일반 (15,000원)</label>
                                <input type="number" id="cnt-adult" placeholder="0" min="0" max="8" onchange="calcPrice()">
                            </div>
                            <div class="p-group">
                                <label>청소년 (12,000원)</label>
                                <input type="number" id="cnt-teen" placeholder="0" min="0" max="8" onchange="calcPrice()">
                            </div>
                            <div class="p-group">
                                <label>우대 (5,000원)</label>
                                <input type="number" id="cnt-pref" placeholder="0" min="0" max="8" onchange="calcPrice()">
                            </div>
                            <div class="p-group">
                                <label>경로 (7,000원)</label>
                                <input type="number" id="cnt-senior" placeholder="0" min="0" max="8" onchange="calcPrice()">
                            </div>
                        </div>
                        <p class="warn-msg">* 우대, 경로 요금은 조조 중복 할인이 불가합니다.</p>
                    </div>

                    <div class="payment-box">
                        <div class="total-price-area">
                            <span>최종 결제 금액</span>
                            <span class="price-display" id="total-price">0원</span>
                        </div>
                        <button class="btn-submit-booking" onclick="submitBooking()">예매하기</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="js/reservation.js"></script>

</body>
</html>