<?php
$userAvatar = session()->get('user_avatar');
$avatarUrl = $userAvatar ? base_url(esc($userAvatar)) : base_url('images/default-avatar.png');
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url() ?>">
            <img src="<?= base_url('images/logo/owl.png') ?>" alt="ShareHub Logo" style="width: 120px; height: auto;">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?= current_url() == base_url() ? 'active' : '' ?>" href="<?= base_url() ?>">
                        หน้าแรก
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= current_url() == base_url('share') ? 'active' : '' ?>" href="<?= base_url('share') ?>">
                        แบ่งปัน
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= current_url() == base_url('post') ? 'active' : '' ?>" href="<?= base_url('post') ?>">
                        ความรู้
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= current_url() == base_url('tag') ? 'active' : '' ?>" href="<?= base_url('tag') ?>">
                        แท็ก
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= current_url() == base_url('category') ? 'active' : '' ?>" href="<?= base_url('category') ?>">
                        หมวดหมู่
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="aboutDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        เกี่ยวกับ
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="aboutDropdown">
                        <li><a class="dropdown-item" href="<?= base_url('about/owl-share') ?>">OWL Share คือ</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('about/contact') ?>">ติดต่อ</a></li>
                    </ul>
                </li>
            </ul>
        </div>

        <div class="d-flex ms-auto">
            <?php if (session()->get('user_id')): ?>
                <!-- Notifications -->
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="badge text-danger"><?= session()->get('unread_notifications', 0) > 0 ? session()->get('unread_notifications') : '' ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                            <div class="notification-list" style="min-width: 300px; max-height: 400px; overflow-y: auto;">
                                <?php if (!empty($notifications)): ?>
                                    <?php foreach ($notifications as $notification): ?>
                                        <a class="dropdown-item <?= !$notification['read_at'] ? 'bg-light' : '' ?>"
                                            href="<?= base_url('notifications/read/' . $notification['id']) ?>">
                                            <small class="text-muted float-end">
                                                <?= time_ago($notification['created_at']) ?>
                                            </small>
                                            <div><?= $notification['message'] ?></div>
                                        </a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="dropdown-item text-center text-muted">
                                        ไม่มีการแจ้งเตือนใหม่
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center" href="<?= base_url('notifications') ?>">ดูทั้งหมด</a>
                        </div>
                    </li>

                    <!-- User Menu -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?= $avatarUrl ?>" alt="<?= esc(session()->get('user_name')) ?>" class="rounded-circle me-2" style="width: 30px; height: 30px;">
                            <span class="d-none d-md-inline-block"><?= esc(session()->get('user_name')) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="<?= base_url('profile') ?>">โปรไฟล์</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('profile/settings') ?>">ตั้งค่า</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="<?= base_url('auth/logout') ?>">ออกจากระบบ</a></li>
                        </ul>
                    </li>
                </ul>
            <?php else: ?>
                <a class="btn btn-outline-primary" href="<?= base_url('auth/login') ?>">
                    เข้าสู่ระบบ
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
    /* Apply font weight light to the navbar links */
    .navbar-nav .nav-link {
        font-weight: 300;
        /* font weight light */
    }

    /* Ensure navbar items are centered on mobile */
    .navbar-nav {
        flex-grow: 1;
        justify-content: center;
    }

    /* Keep the login button and profile dropdown on the right on all screen sizes */
    .d-flex.ms-auto {
        display: flex;
        align-items: center;
        margin-left: auto;
    }

    /* Mobile responsiveness: Adjustments for smaller screens */
    @media (max-width: 991px) {
        .navbar-collapse {
            text-align: center;
        }

        .navbar-nav {
            flex-direction: column;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>