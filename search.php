<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'inc/db_conn.php';
include_once 'inc/icons.php'; 

// 검색어 받기
$keyword = isset($_GET['q']) ? $_GET['q'] : '';
$search_term = trim($keyword); 

// 로그인 유저 확인
$user_id = isset($_SESSION['userid']) ? $_SESSION['userid'] : null;
$user_db_id = 0;

if ($user_id) {
    $sql_u = "SELECT id FROM users WHERE userid = '$user_id'";
    $res_u = mysqli_query($conn, $sql_u);
    if ($row_u = mysqli_fetch_assoc($res_u)) {
        $user_db_id = $row_u['id'];
    }
}

// 좋아요 확인 함수
function isLiked($conn, $user_db_id, $movie_id) {
    if ($user_db_id == 0) return false;
    $sql = "SELECT id FROM wishlist WHERE user_id = $user_db_id AND movie_id = $movie_id";
    $result = mysqli_query($conn, $sql);
    return mysqli_num_rows($result) > 0;
}

// 검색 로직
$search_nospace = str_replace(' ', '', $search_term);
$search_safe = mysqli_real_escape_string($conn, $search_nospace);

$sql = "SELECT * FROM movies 
        WHERE REPLACE(title, ' ', '') LIKE '%$search_safe%' 
        ORDER BY release_date DESC";

$result = mysqli_query($conn, $sql);
$count = mysqli_num_rows($result);

$today = date("Y-m-d");
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>'<?= htmlspecialchars($keyword) ?>' 검색 결과 - LimeLight</title>
    
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/search.css">
</head>
<body>

    <div class="layout-wrapper">
        <?php include 'inc/sidebar.php'; ?>

        <main class="main-content home-bg">
            
            <div class="search-header">
                <h2 class="search-result-title">
                    "<span class="highlight"><?= htmlspecialchars($keyword) ?></span>" 검색 결과
                    <span class="count">(<?= $count ?>건)</span>
                </h2>
            </div>

            <div class="search-grid">
                <?php 
                if ($count > 0) {
                    while($row = mysqli_fetch_assoc($result)): 
                        $is_liked = isLiked($conn, $user_db_id, $row['id']);
                        $active_class = $is_liked ? 'active' : '';
                        $movie_title = addslashes($row['title']);
                        if ($user_id) {
                            $onclick_action = "openReviewModal({$row['id']}, '$movie_title')";
                        } else {
                            $onclick_action = "if(confirm('로그인이 필요한 서비스입니다.\\n로그인 페이지로 이동하시겠습니까?')) location.href='login.php';";
                        }
                ?>
                    <div class="movie-card">
                        <div class="poster-wrapper">
                            <img src="<?= $row['poster_img'] ?>" 
                                 alt="<?= $row['title'] ?>" 
                                 class="poster-img click-trigger"
                                 onclick="<?= $onclick_action ?>">

                            <?php if ($user_id): ?>
                                <button class="btn-like <?= $active_class ?>" 
                                        onclick="toggleLike(this, <?= $row['id'] ?>)">
                                    <?= getIconHeartButton() ?>
                                </button>
                            <?php endif; ?>
                        </div>
                        
                        <div class="movie-info">
                            <h3 class="movie-title"><?= $row['title'] ?></h3>
                            
                            <?php if ($row['release_date'] > $today): ?>
                                <p class="release-date"><?= $row['release_date'] ?> 개봉</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php 
                    endwhile; 
                } else { 
                ?>
                    <div class="no-result">
                        <p>검색 결과가 없습니다.</p>
                        <p class="sub-text">다른 키워드로 검색해 보세요.</p>
                    </div>
                <?php } ?>
            </div>

        </main>
    </div>

    <div id="review-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modal-movie-title"></h2>
                <button class="btn-close" onclick="closeReviewModal()">
                    <?= getIconClose() ?>
                </button>
            </div>
            
            <div class="review-input-area">
                <textarea id="review-text" placeholder="이 영화에 대한 한줄평을 남겨주세요. (최대 50자)" maxlength="50"></textarea>
                <button class="btn-submit-review" onclick="submitReview()">등록</button>
            </div>

            <div class="review-list-area">
                <h3 class="review-list-title">관람객 한줄평</h3>
                <div id="review-list-container"></div>
            </div>
        </div>
    </div>

    <script src="js/home.js"></script> 

</body>
</html>