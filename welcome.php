<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$join_name = isset($_SESSION['join_name']) ? $_SESSION['join_name'] : '회원';
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>환영합니다 - LimeLight</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/welcome.css">
</head>
<body>
    <div class="layout-wrapper">
        <?php include 'inc/sidebar.php'; ?>
        <main class="main-content welcome-page-bg">
            <div class="welcome-container">
                <h1 class="welcome-title">WELCOME!</h1>
                <div class="welcome-message-box">
                    <p class="user-greeting">
                        <span class="highlight-name"><?= $join_name ?></span>님, 환영합니다!
                    </p>   
                    <p class="service-text">
                        이제 <span class="brand-name">LimeLight</span><br>
                        영화 예매 서비스를 이용하실 수 있습니다.
                    </p>
                </div>
                <a href="login.php" class="btn-go-login">로그인</a>              
            </div>
        </main>
    </div>
</body>
</html>