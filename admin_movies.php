<?php
session_start();
include 'inc/db_conn.php';

// 로그인하지 않았거나, role이 admin이 아니면 홈으로 튕겨냄
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('관리자만 접근 가능합니다.'); location.href='index.php';</script>";
    exit;
}

// 영화 등록
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $runtime = (int)$_POST['runtime'];
    $release_date = $_POST['release_date'];
    // 체크박스가 체크되어 있으면 1, 아니면 0
    $is_showing = isset($_POST['is_showing']) ? 1 : 0;

    // 파일 업로드 처리
    $poster_path = '';
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
        // 업로드 폴더 확인 및 생성
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // 파일명 중복 방지
        $ext = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
        $filename = "poster_" . time() . "." . $ext; 
        $poster_path = $upload_dir . $filename;
        
        // 임시 파일을 실제 폴더로 이동
        if (!move_uploaded_file($_FILES['poster']['tmp_name'], $poster_path)) {
             echo "<script>alert('포스터 업로드 실패'); history.back();</script>";
             exit;
        }
    }

    // DB 저장
    $sql = "INSERT INTO movies (title, poster_img, runtime, release_date, is_showing) 
            VALUES ('$title', '$poster_path', '$runtime', '$release_date', '$is_showing')";
            
    if(mysqli_query($conn, $sql)) {
        echo "<script>alert('영화가 등록되었습니다.'); location.href='admin_movies.php';</script>";
    } else {
        echo "<script>alert('DB 오류: " . mysqli_error($conn) . "'); history.back();</script>";
    }
    exit;
}

// 영화 삭제
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $sql_img = "SELECT poster_img FROM movies WHERE id = $id";
    $res_img = mysqli_query($conn, $sql_img);
    if($row_img = mysqli_fetch_assoc($res_img)) {
        if(file_exists($row_img['poster_img'])) {
            unlink($row_img['poster_img']); // 파일 삭제
        }
    }

    // DB 데이터 삭제
    mysqli_query($conn, "DELETE FROM movies WHERE id=$id");
    echo "<script>alert('삭제되었습니다.'); location.href='admin_movies.php';</script>";
    exit;
}

if (isset($_GET['status_id']) && isset($_GET['new_status'])) {
    $id = (int)$_GET['status_id'];
    $new_status = (int)$_GET['new_status'];

    $sql = "UPDATE movies SET is_showing = $new_status WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        $status_text = $new_status == 1 ? '상영중' : '종영';
        echo "<script>alert('상태가 {$status_text}으로 변경되었습니다.'); location.href='admin_movies.php';</script>";
    } else {
        echo "<script>alert('상태 변경 실패: " . mysqli_error($conn) . "'); history.back();</script>";
    }
    exit;
}

// 영화 목록 조회 - 최신 등록순
$result = mysqli_query($conn, "SELECT * FROM movies ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>영화 관리 - LimeLight</title>
    
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="layout-wrapper">
        <?php include 'inc/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="admin-content">
                <h2 class="admin-page-title">영화 관리</h2>
                <p class="admin-desc">영화 등록 / 삭제 / 상태 수정</p>

                <div class="form-box">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="input-row">
                            <input type="text" name="title" placeholder="영화 제목" required>
                            <input type="number" name="runtime" placeholder="상영시간(분)" required>
                        </div>
                        
                        <div class="input-row">
                            <input type="date" name="release_date" required>

                            <label class="checkbox-label">
                                <input type="checkbox" name="is_showing" checked> 
                                <span>상영중</span>
                            </label>
                        </div>

                        <div class="input-row file-input-group">
                            <span>포스터: </span>
                            <input type="file" name="poster" accept="image/*" required>
                        </div>

                        <button type="submit" class="btn-submit">영화 등록</button>
                    </form>
                </div>

                <table class="list-table">
                    <thead>
                        <tr>
                            <th width="10%">ID</th>
                            <th width="15%">포스터</th>
                            <th width="35%">제목</th>
                            <th width="15%">개봉일</th>
                            <th width="10%">상태</th>
                            <th width="15%">관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td>
                                <?php if($row['poster_img']): ?>
                                    <img src="<?= $row['poster_img'] ?>" width="40" style="border-radius:4px;">
                                <?php else: ?>
                                    <span style="font-size:12px; color:#777;">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= $row['release_date'] ?></td>
                            <td>
                                <?php if($row['is_showing']): ?>
                                    <span style="color:#CFFF04;">상영중</span>
                                <?php else: ?>
                                    <span style="color:#777;">종영</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px; align-items: center;"> 
                                    <?php if($row['is_showing']): ?>
                                    <a href="?status_id=<?= $row['id'] ?>&new_status=0" 
                                       class="btn-status btn-status-off" 
                                       onclick="return confirm('정말 종영 처리하시겠습니까?\n(상영 시간표에서 제외됩니다)')">종영 처리</a>
                                    <?php else: ?>
                                    <a href="?status_id=<?= $row['id'] ?>&new_status=1" 
                                       class="btn-status btn-status-on" 
                                       onclick="return confirm('상영중으로 변경하시겠습니까?')">상영 재개</a>
                                    <?php endif; ?>

                                    <a href="?delete_id=<?= $row['id'] ?>" class="btn-del" onclick="return confirm('정말 삭제하시겠습니까?\n(관련된 상영 시간표 및 예매 내역도 모두 삭제됩니다)')">삭제</a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        
                        <?php if (mysqli_num_rows($result) == 0): ?>
                            <tr><td colspan="6" style="text-align:center; padding:30px;">등록된 영화가 없습니다.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
            </div>
        </main>
    </div>
</body>
</html>