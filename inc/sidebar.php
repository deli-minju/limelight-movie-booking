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

        $sql_wish = "SELECT m.title, m.id as movie_id 
                     FROM wishlist w 
                     JOIN movies m ON w.movie_id = m.id 
                     WHERE w.user_id = '$user_db_id' 
                     ORDER BY w.created_at DESC";
        $result_wish = mysqli_query($conn, $sql_wish);
    }
}

$current_page = basename($_SERVER['PHP_SELF']); 
?>

<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/sidebar.css">

<div id="edit-overlay" class="edit-overlay"></div>

<aside class="sidebar-container" id="sidebar">
    <div class="logo-area">
        <a href="index.php">
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
            <li class="menu-item <?= ($current_page == 'index.php' || $current_page == '') ? 'active' : '' ?>">
                <a href="index.php">
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
            <?php if ($is_login): ?>
                <li class="menu-item <?= ($current_page == 'profile.php') ? 'active' : '' ?>">
                    <a href="profile.php">
                        <span class="menu-icon"><?= getIconProfile() ?></span>
                        <span class="menu-text">프로필</span>
                    </a>
                </li>
            <?php else: ?>
                <li class="menu-item <?= ($current_page == 'login.php' || $current_page == 'register.php') ? 'active' : '' ?>">
                    <a href="login.php">
                        <span class="menu-icon"><?= getIconProfile() ?></span>
                        <span class="menu-text">로그인 • 회원가입</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

    <?php if ($is_login): ?>
    <div class="my-lists-area" id="my-lists-area">
        <div class="section-title">MY LISTS</div>
        
        <ul class="lists-wrapper" id="wishlist-ul">
            <?php 
            if ($result_wish && mysqli_num_rows($result_wish) > 0) {
                while ($row_wish = mysqli_fetch_assoc($result_wish)) { 
            ?>
                <li class="list-item" data-id="<?= $row_wish['movie_id'] ?>">               
                    <span class="list-icon icon-heart-view">
                        <?= getIconHeart() ?>
                    </span>

                    <span class="list-icon icon-delete-btn" onclick="deleteWishItem(<?= $row_wish['movie_id'] ?>, this)">
                        <?= getIconDelete() ?>
                    </span>

                    <span class="list-text" title="<?= $row_wish['title'] ?>">
                        <?= $row_wish['title'] ?>
                    </span>
                </li>
            <?php 
                } 
            } else { 
            ?>
                <li class="list-item empty-msg" style="color: #777; font-size: 13px; justify-content: center;">
                    아직 찜한 영화가 없어요.
                </li>
            <?php } ?>
        </ul>

        <div class="cta-buttons">
            <button class="btn-edit" id="btn-edit-list" onclick="toggleEditMode()">
                <span class="btn-icon"><?= getIconEdit() ?></span>
                <span class="btn-text">리스트 편집</span>
            </button>
            
            <?php if ($is_admin): ?>
            <a href="member-list.php" class="btn-member-list">
                회원 목록 보기
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($is_login): ?>
    <div class="user-footer">
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

<script src="js/sidebar.js"></script>