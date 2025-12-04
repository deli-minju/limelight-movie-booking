<?php
session_start();
include 'inc/db_conn.php';

// 관리자 권한 체크
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('관리자만 접근 가능합니다.'); location.href='index.php';</script>";
    exit;
}

// 지점 등록
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);

    if (!empty($name) && !empty($location)) {
        $sql = "INSERT INTO theaters (name, location) VALUES ('$name', '$location')";
        if(mysqli_query($conn, $sql)) {
            echo "<script>alert('지점이 등록되었습니다.'); location.href='admin_theaters.php';</script>";
        } else {
            echo "<script>alert('등록 실패: " . mysqli_error($conn) . "'); history.back();</script>";
        }
    }
    exit;
}

// 지점 삭제
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $sql = "DELETE FROM theaters WHERE id = $id";
    if(mysqli_query($conn, $sql)) {
        echo "<script>alert('삭제되었습니다.'); location.href='admin_theaters.php';</script>";
    } else {
        echo "<script>alert('삭제 실패: " . mysqli_error($conn) . "'); history.back();</script>";
    }
    exit;
}

// 지점 목록 조회
$result = mysqli_query($conn, "SELECT * FROM theaters ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>지점 관리 - LimeLight</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="layout-wrapper">
        <?php include 'inc/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="admin-content">
                
                <!-- 제목 -->
                <h2 class="admin-page-title">지점 관리</h2>
                <p class="admin-desc">지점을 등록하거나 삭제합니다.</p>

                <!-- 지점 등록 폼 -->
                <div class="form-box">
                    <form method="post">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="input-row">
                            <input type="text" name="name" placeholder="지점명 (예: 강남점)" required>
                            <input type="text" name="location" placeholder="위치/주소 (예: 서울 강남구)" required>
                        </div>

                        <button type="submit" class="btn-submit">지점 등록</button>
                    </form>
                </div>

                <!-- 등록된 지점 목록 -->
                <h3 style="margin-bottom:15px; font-size:18px; color:#CFFF04;">등록된 지점 목록</h3>
                
                <table class="list-table">
                    <thead>
                        <tr>
                            <th width="15%">ID</th>
                            <th width="35%">지점명</th>
                            <th width="35%">위치</th>
                            <th width="15%">관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['location']) ?></td>
                            <td>
                                <!-- 삭제 버튼 -->
                                <a href="?delete_id=<?= $row['id'] ?>" class="btn-del" onclick="return confirm('정말 삭제하시겠습니까?\n(해당 지점의 모든 상영 시간표가 함께 삭제됩니다)')">삭제</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        
                        <?php if (mysqli_num_rows($result) == 0): ?>
                            <tr><td colspan="4" style="text-align:center; padding:30px;">등록된 지점이 없습니다.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
            </div>
        </main>
    </div>
</body>
</html>