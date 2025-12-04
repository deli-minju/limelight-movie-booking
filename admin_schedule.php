<?php
session_start();
include 'inc/db_conn.php';
date_default_timezone_set('Asia/Seoul');

// 관리자 권한 체크
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('관리자만 접근 가능합니다.'); location.href='index.php';</script>";
    exit;
}

// 삭제 제한 날짜 (오늘 + 7일)
$today = date('Y-m-d');
$lock_date = date('Y-m-d', strtotime('+7 days'));

// 등록 시작 날짜 (오늘 + 1일)
$start_date = date('Y-m-d', strtotime('+1 day'));

// 스케줄 등록
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $movie_id = $_POST['movie_id'];
    $theater_id = $_POST['theater_id'];
    $screen_name = mysqli_real_escape_string($conn, $_POST['screen_name']);
    $date = $_POST['date'];
    $time = $_POST['time'];

    $sql_release = "SELECT release_date FROM movies WHERE id = $movie_id";
    $res_release = mysqli_query($conn, $sql_release);
    if ($res_release && $row_release = mysqli_fetch_assoc($res_release)) {
        $release_date = $row_release['release_date'];

        if ($date < $release_date) {
            echo "<script>alert('{$release_date} 이전에 상영 스케줄을 등록할 수 없습니다.'); history.back();</script>";
            exit;
        }
    }
    
    $start_time = "$date $time:00";

    $sql = "INSERT INTO showtimes (movie_id, theater_id, start_time, screen_name) 
            VALUES ('$movie_id', '$theater_id', '$start_time', '$screen_name')";
            
    if(mysqli_query($conn, $sql)) {
        echo "<script>alert('스케줄이 등록되었습니다.'); location.href='admin_schedule.php';</script>";
    } else {
        echo "<script>alert('등록 실패: " . mysqli_error($conn) . "'); history.back();</script>";
    }
    exit;
}

// 스케줄 삭제
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    // 해당 스케줄의 날짜 확인
    $chk_sql = "SELECT start_time FROM showtimes WHERE id = $id";
    $chk_res = mysqli_query($conn, $chk_sql);
    $row = mysqli_fetch_assoc($chk_res);
    
    if ($row) {
        $show_date = date('Y-m-d', strtotime($row['start_time']));

        // 수정/삭제 제한 로직
        if ($show_date < $lock_date) {
            echo "<script>alert('이미 예매가 오픈된 기간($lock_date 이전)의 정보는 삭제할 수 없습니다.'); history.back();</script>";
        } else {
            mysqli_query($conn, "DELETE FROM showtimes WHERE id=$id");
            echo "<script>alert('삭제되었습니다.'); location.href='admin_schedule.php';</script>";
        }
    }
    exit;
}

// 데이터 불러오기
$movies = mysqli_query($conn, "SELECT * FROM movies WHERE is_showing = 1 AND is_deleted = 0 ORDER BY title ASC");
$theaters = mysqli_query($conn, "SELECT * FROM theaters WHERE is_deleted = 0 ORDER BY name ASC");

// 수정 가능한 스케줄만 조회하여 리스트에 표시
$schedules = mysqli_query($conn, "SELECT s.*, m.title, t.name as theater_name 
                                  FROM showtimes s 
                                  JOIN movies m ON s.movie_id = m.id 
                                  JOIN theaters t ON s.theater_id = t.id 
                                  WHERE DATE(s.start_time) >= '$lock_date' 
                                  ORDER BY s.start_time ASC");
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>스케줄 관리 - LimeLight</title>
    
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/admin.css">
    
    <style>
        .schedule-list { display: grid; gap: 10px; }
        .schedule-item { 
            background: #222; padding: 15px; border-radius: 8px; 
            display: flex; justify-content: space-between; align-items: center; 
            border: 1px solid #333;
        }
        .info span { margin-right: 10px; color: #ccc; font-size: 14px; }
        .info strong { color: #CFFF04; margin-right: 10px; font-size: 16px; }
        
        .btn-del.disabled { 
            border-color: #555; color: #555; cursor: not-allowed; pointer-events: none; 
        }
    </style>
</head>
<body>
    <div class="layout-wrapper">
        <?php include 'inc/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="admin-content">
                <h2 class="admin-page-title">스케줄 관리</h2>
                <p class="admin-desc">
                    상영 시간표 편성 / 삭제<br>
                    <span style="color:#666; font-size:13px;">
                        * 등록은 <strong style="color:#CFFF04"><?= $start_date ?></strong> 스케줄부터 가능합니다.
                        <br>* 삭제는 <strong style="color:#F33F3F"><?= $lock_date ?></strong> 이후의 스케줄만 가능합니다.
                    </span>
                </p>

                <div class="form-box">
                    <form method="post">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="input-row">
                            <select name="theater_id" required>
                                <option value="">극장 선택</option>
                                <?php while($t = mysqli_fetch_assoc($theaters)): ?>
                                    <option value="<?= $t['id'] ?>"><?= $t['name'] ?></option>
                                <?php endwhile; ?>
                            </select>
                            
                            <select name="movie_id" required>
                                <option value="">영화 선택</option>
                                <?php 
                                while($m = mysqli_fetch_assoc($movies)): 
                                ?>
                                    <option value="<?= $m['id'] ?>"><?= $m['title'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="input-row">
                            <input type="text" name="screen_name" placeholder="상영관 (예: 1관 IMAX)" required>
                            <input type="date" name="date" min="<?= $start_date ?>" required>
                            <input type="time" name="time" required>
                        </div>

                        <button type="submit" class="btn-submit">스케줄 등록</button>
                    </form>
                </div>

                <h3 style="margin-bottom:15px; font-size:18px; color:#CFFF04;">삭제 가능한 스케줄 목록</h3>
                
                <div class="schedule-list">
                    <?php if(mysqli_num_rows($schedules) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($schedules)): 
                            $s_date = date('Y-m-d', strtotime($row['start_time']));
                            $can_delete = ($s_date >= $lock_date);
                            $korean_day = array("일", "월", "화", "수", "목", "금", "토");
                            $day_of_week_num = date('w', strtotime($row['start_time'])); 
                            $korean_day_str = $korean_day[$day_of_week_num];
                        ?>
                        <div class="schedule-item">
                            <div class="info">
                                <!-- 날짜 및 시간 -->
                                <strong><?= date('m.d', strtotime($row['start_time'])) ?> (<?= $korean_day_str ?>) <?= date('H:i', strtotime($row['start_time'])) ?></strong>
                                
                                <!-- 극장 및 상영관 -->
                                <span style="color:#fff; font-weight:bold;"><?= $row['theater_name'] ?></span>
                                <span><?= $row['screen_name'] ?></span>
                                
                                <!-- 영화 제목 -->
                                <span style="color:#CFFF04;">[<?= $row['title'] ?>]</span>
                            </div>
                            
                            <!-- 삭제 버튼 -->
                            <?php if ($can_delete): ?>
                                <a href="?delete_id=<?= $row['id'] ?>" class="btn-del" onclick="return confirm('정말 삭제하시겠습니까?')">삭제</a>
                            <?php else: ?>
                                <span class="btn-del disabled">수정불가</span>
                            <?php endif; ?>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div style="text-align:center; padding:40px; color:#777; background:#1e1e1e; border-radius:8px;">
                            삭제 가능한 스케줄이 없습니다.
                        </div>
                    <?php endif; ?>
                </div>
                
            </div>
        </main>
    </div>
</body>
</html>