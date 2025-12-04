<?php
session_start();
include 'inc/db_conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>
        alert('관리자만 접근 가능한 페이지입니다.'); 
        location.href='index.php';
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>관리자 페이지 - LimeLight</title>
    
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

    <div class="layout-wrapper">
        <?php include 'inc/sidebar.php'; ?>

        <main class="main-content">
            <div class="admin-container">
                
                <div class="admin-header">
                    <h1 class="admin-page-title" style="font-size:32px; margin-bottom: 10px;">LimeLight 관리</h1>
                    <p class="admin-desc">관리자 전용 대시보드입니다. 메뉴를 선택하세요.</p>
                </div>

                <div class="admin-menu-grid">
                    <a href="admin_movies.php" class="menu-card">
                        <span class="menu-card-icon">🎬</span>
                        <span class="menu-title">영화 관리</span>
                        <span class="menu-desc">
                            영화를 등록하거나 삭제합니다.
                        </span>
                    </a>

                    <a href="admin_theaters.php" class="menu-card">
                        <span class="menu-card-icon">🏢</span>
                        <span class="menu-title">지점 관리</span>
                        <span class="menu-desc">
                            지점을 등록하거나 삭제합니다.
                        </span>
                    </a>

                    <a href="admin_schedule.php" class="menu-card">
                        <span class="menu-card-icon">📅</span>
                        <span class="menu-title">스케줄 관리</span>
                        <span class="menu-desc">
                            상영 시간표를 편성하거나 삭제합니다.
                        </span>
                    </a>
                </div>

            </div>
        </main>
    </div>

</body>
</html>