<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'inc/db_conn.php';
include_once 'inc/icons.php';

$user_id = isset($_SESSION['userid']) ? $_SESSION['userid'] : null;
$user_db_id = 0; 

if ($user_id) {
    $sql_u = "SELECT id FROM users WHERE userid = '$user_id'";
    $res_u = mysqli_query($conn, $sql_u);
    if ($row_u = mysqli_fetch_assoc($res_u)) {
        $user_db_id = $row_u['id'];
    }
}

// [무비차트] 현재 상영중인 영화
$sql_now = "SELECT * FROM movies WHERE is_showing = 1 AND release_date <= CURDATE() ORDER BY release_date DESC";
$result_now = mysqli_query($conn, $sql_now);

// [상영예정] 개봉 예정 영화
$sql_soon = "SELECT *, DATEDIFF(release_date, CURDATE()) as d_day 
             FROM movies 
             WHERE release_date > CURDATE() 
             ORDER BY release_date ASC";
$result_soon = mysqli_query($conn, $sql_soon);

// 좋아요 여부 확인 함수
function isLiked($conn, $user_db_id, $movie_id) {
    if ($user_db_id == 0) return false;
    $sql = "SELECT id FROM wishlist WHERE user_id = $user_db_id AND movie_id = $movie_id";
    $result = mysqli_query($conn, $sql);
    return mysqli_num_rows($result) > 0;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LimeLight - Home</title>
    
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/home.css"> 
</head>
<body>

    <div class="layout-wrapper">
        <?php include 'inc/sidebar.php'; ?>

        <main class="main-content home-bg">
            
            <div class="hero-section">
                <h1 class="hero-title">
                    <span class="text-white">Step into the</span> 
                    <span class="text-lime">LimeLight</span>
                </h1>
            </div>

            <section class="movie-section">
                <h2 class="home-section-title">무비차트</h2>
                
                <div class="slider-container">
                    <button class="slider-btn prev-btn" onclick="slide('now', -1)">‹</button>
                    
                    <div class="slider-track" id="track-now">
                        <?php while($row = mysqli_fetch_assoc($result_now)): 
                            $is_liked = isLiked($conn, $user_db_id, $row['id']);
                            $active_class = $is_liked ? 'active' : '';
                        ?>
                        <div class="movie-card">
                            <div class="poster-wrapper">
                                <img src="<?= $row['poster_img'] ?>" 
                                     alt="<?= $row['title'] ?>" 
                                     class="poster-img click-trigger"
                                     data-id="<?= $row['id'] ?>"
                                     data-title="<?= htmlspecialchars($row['title']) ?>">
                                
                                <?php if ($user_id): ?>
                                    <button class="btn-like <?= $active_class ?>" 
                                            onclick="toggleLike(this, <?= $row['id'] ?>)">
                                        <?= getIconHeartButton() ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                            <div class="movie-info">
                                <h3 class="movie-title"><?= $row['title'] ?></h3>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>

                    <button class="slider-btn next-btn" onclick="slide('now', 1)">›</button>
                </div>
            </section>

            <section class="movie-section">
                <h2 class="home-section-title">상영예정</h2>
                
                <div class="slider-container">
                    <button class="slider-btn prev-btn" onclick="slide('soon', -1)">‹</button>
                    
                    <div class="slider-track" id="track-soon">
                        <?php while($row = mysqli_fetch_assoc($result_soon)): 
                             $is_liked = isLiked($conn, $user_db_id, $row['id']);
                             $active_class = $is_liked ? 'active' : '';
                        ?>
                        <div class="movie-card">
                            <div class="poster-wrapper">
                                <img src="<?= $row['poster_img'] ?>" 
                                     alt="<?= $row['title'] ?>" 
                                     class="poster-img click-trigger"
                                     data-id="<?= $row['id'] ?>"
                                     data-title="<?= htmlspecialchars($row['title']) ?>">
                                
                                <?php if ($user_id): ?>
                                    <button class="btn-like <?= $active_class ?>" 
                                            onclick="toggleLike(this, <?= $row['id'] ?>)">
                                        <?= getIconHeartButton() ?>
                                    </button>
                                <?php endif; ?>

                                <div class="d-day-label">D-<?= $row['d_day'] ?></div>
                            </div>
                            <div class="movie-info">
                                <h3 class="movie-title"><?= $row['title'] ?></h3>
                                <p class="release-date"><?= $row['release_date'] ?> 개봉</p>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>

                    <button class="slider-btn next-btn" onclick="slide('soon', 1)">›</button>
                </div>
            </section>

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