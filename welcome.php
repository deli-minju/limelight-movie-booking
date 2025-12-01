<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>Welcome - LimeLight</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: #000;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
            font-family: 'Pretendard', sans-serif;
        }
        .welcome-box {
            background: #1e1e1e;
            padding: 50px;
            border-radius: 16px;
            border: 1px solid #CFFF04;
            box-shadow: 0 0 20px rgba(207, 255, 4, 0.2);
        }
        h1 { color: #CFFF04; margin-bottom: 20px; }
        p { color: #eaeaea; margin-bottom: 30px; }
        .btn-go-home {
            background: linear-gradient(90deg, #cfff04, #b6e710);
            color: #2c2c2c;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="welcome-box">
        <h1>WELCOME TO LIMELIGHT!</h1>
        <p>회원가입이 성공적으로 완료되었습니다.<br>이제 LimeLight의 모든 서비스를 이용해보세요.</p>
        <a href="login.php" class="btn-go-home">홈으로 이동</a>
    </div>
</body>
</html>