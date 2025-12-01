<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원가입 - LimeLight</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <div class="layout-wrapper">
        <?php include 'inc/sidebar.php'; ?>
        <main class="main-content register-page-bg">
            <div class="register-container">
                <h1 class="register-title">SIGN UP</h1>
                <form action="register_ok.php" method="post" class="register-form" onsubmit="return validateForm()">  
                    <div class="form-group">
                        <label class="form-label">아이디</label>
                        <div class="input-with-btn">
                            <input type="text" name="userid" id="userid" class="input-field" placeholder="아이디 입력" required>
                            <button type="button" class="btn-duplicate" onclick="checkId()">중복 확인</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">비밀번호</label>
                        <input type="password" name="password" id="password" class="input-field" placeholder="비밀번호 입력" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">비밀번호 확인</label>
                        <input type="password" id="password_confirm" class="input-field" placeholder="비밀번호 재입력" required>
                        <span id="pw-msg" class="msg-text"></span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">이름</label>
                        <input type="text" name="name" class="input-field" placeholder="이름 입력" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">닉네임</label>
                        <div class="input-with-btn">
                            <input type="text" name="nickname" id="nickname" class="input-field" placeholder="닉네임 입력" required>
                            <button type="button" class="btn-duplicate" onclick="checkNickname()">중복 확인</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">이메일</label>
                        <input type="email" name="email" id="email" class="input-field" placeholder="example@gmail.com" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">생년월일</label>
                        <div class="birth-container" id="birth-box">
                            <input type="text" name="birth_year" class="birth-input" placeholder="YYYY" maxlength="4">
                            <span class="slash">/</span>
                            <input type="text" name="birth_month" class="birth-input" placeholder="MM" maxlength="2">
                            <span class="slash">/</span>
                            <input type="text" name="birth_day" class="birth-input" placeholder="DD" maxlength="2">
                        </div>
                    </div>
                    <button type="submit" class="btn-submit">회원가입</button>
                </form>
            </div>
        </main>
    </div>

    <script src="js/register.js"></script>

</body>
</html>