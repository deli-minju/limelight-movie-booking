<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 이미 로그인된 회원이면 홈으로 이동
if (isset($_SESSION['userid'])) {
    echo "<script>
        alert('이미 로그인되어 있습니다.');
        location.href = 'index.php';
    </script>";
    exit;
}

$has_error = isset($_GET['error']); 
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인 - LimeLight</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="layout-wrapper">
        
        <?php include 'inc/sidebar.php'; ?>

        <main class="main-content login-page-bg">
            <div class="login-container">
                <h1 class="login-title">LOGIN</h1>
                
                <form action="login_ok.php" method="post" class="login-form">
                    
                    <div class="input-group">
                        <input type="text" name="userid" 
                               class="input-field <?= $has_error ? 'error' : '' ?>" 
                               placeholder="아이디 입력" required>
                    </div>

                    <div class="input-group">
                        <input type="password" name="password" 
                               class="input-field <?= $has_error ? 'error' : '' ?>" 
                               placeholder="비밀번호 입력" required>
                    </div>

                    <?php if ($has_error): ?>
                        <div style="color: #F33F3F; font-size: 13px; text-align: center; margin-top: 10px;">
                            아이디 또는 비밀번호가 일치하지 않습니다.
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn-login">로그인</button>
                    
                </form>

                <div class="signup-link-area">
                    <a href="register.php" class="btn-signup">회원가입</a>
                </div>

            </div>
        </main>
    </div>

</body>
</html>