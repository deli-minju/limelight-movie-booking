<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once 'inc/db_conn.php';
include_once 'inc/icons.php';

$is_login = false;
$is_admin = false;
$user_name = "";
$user_email = "";
$user_img = "img/profile-default.png";
$result_wish = null;

if (isset($_SESSION['userid'])) {
    $is_login = true;
    $session_id = $_SESSION['userid'];

    $sql_user = "SELECT id, username, nickname, email, role, profile_img FROM users WHERE userid = '$session_id'";
    $result_user = mysqli_query($conn, $sql_user);

    if ($row_user = mysqli_fetch_assoc($result_user)) {
        $user_db_id = $row_user['id'];
        $user_name = $row_user['nickname'];
        $user_email = $row_user['email'];
        
        if (!empty($row_user['profile_img'])) {
            $user_img = $row_user['profile_img'];
        }

        if ($row_user['role'] === 'admin') {
            $is_admin = true;
        }

        $sql_wish = "SELECT m.title 
                     FROM wishlist w 
                     JOIN movies m ON w.movie_id = m.id 
                     WHERE w.user_id = '$user_db_id' 
                     ORDER BY w.created_at DESC 
                     LIMIT 5";
        $result_wish = mysqli_query($conn, $sql_wish);
    }
}

$current_page = basename($_SERVER['PHP_SELF']); 
?><aside class="sidebar-container">
    <div class="logo-area">
        <a href="home.php">
            <img src="img/logo.png" alt="LimeLight" class="logo-img">
        </a>
    </div>

    <nav class="nav-menu">
        <div class="search-box">
            <div class="search-input-wrapper">
                <span class="search-icon"><?= getIconSearch() ?></span>
                <input type="text" placeholder="Search" class="search-input">
            </div>
        </div>

        <ul class="menu-list">
            <li class="menu-item <?= ($current_page == 'home.php' || $current_page == '') ? 'active' : '' ?>">
                <a href="home.php">
                    <span class="menu-icon"><?= getIconHome() ?></span>
                    <span class="menu-text">홈</span>
                </a>
            </li>

            <li class="menu-item <?= ($current_page == 'reservation.php') ? 'active' : '' ?>">
                <a href="reservation.php">
                    <span class="menu-icon"><?= getIconTicket() ?></span>
                    <span class="menu-text">예매</span>
                </a>
            </li>

            <!-- 로그인 상태에 따른 메뉴 분기 -->
            <?php if ($is_login): ?>
                <li class="menu-item <?= ($current_page == 'profile.php') ? 'active' : '' ?>">
                    <a href="profile.php">
                        <span class="menu-icon"><?= getIconProfile() ?></span>
                        <span class="menu-text">프로필</span>
                    </a>
                </li>
            <?php else: ?>
                <!-- 비로그인 시: 로그인 페이지로 이동 -->
                <li class="menu-item <?= ($current_page == 'login.php' || $current_page == 'register.php') ? 'active' : '' ?>">
                    <a href="login.php">
                        <span class="menu-icon"><?= getIconProfile() ?></span>
                        <span class="menu-text">로그인 • 회원가입</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- MY LISTS (로그인 시에만 DB 조회 결과 표시) -->
    <?php if ($is_login): ?>
    <div class="my-lists-area">
        <div class="section-title">MY LISTS</div>
        <ul class="lists-wrapper">
            <?php 
            if ($result_wish && mysqli_num_rows($result_wish) > 0) {
                while ($row_wish = mysqli_fetch_assoc($result_wish)) { 
            ?>
                <li class="list-item">
                    <span class="list-icon"><?= getIconHeart() ?></span>
                    <span class="list-text">
                        <?= $row_wish['title'] ?>
                    </span>
                </li>
            <?php 
                } 
            } else { 
            ?>
                <li class="list-item" style="color: #777; font-size: 13px; justify-content: center;">
                    아직 찜한 영화가 없어요.
                </li>
            <?php } ?>
        </ul>

        <div class="cta-buttons">
            <button class="btn-edit" onclick="location.href='wishlist.php'">
                <span class="btn-icon"><?= getIconEdit() ?></span>
                리스트 편집
            </button>
            
            <!-- 관리자일 경우에만 보이는 버튼 -->
            <?php if ($is_admin): ?>
            <a href="member-list.php" class="btn-member-list">
                회원 목록 보기
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- 사용자 하단 정보 (로그인 시에만 표시) -->
    <?php if ($is_login): ?>
    <div class="user-footer">
        <!-- DB에서 가져온 프로필 이미지 경로 사용 -->
        <div class="profile-img" style="background-image: url('<?= $user_img ?>');"></div>
        
        <div class="user-info">
            <span class="u-name"><?= $user_name ?></span>
            <span class="u-email"><?= $user_email ?></span>
        </div>
        
        <a href="logout.php" class="btn-logout" title="로그아웃">
            <?= getIconLogout() ?>
        </a>
    </div>
    <?php endif; ?>
</aside>