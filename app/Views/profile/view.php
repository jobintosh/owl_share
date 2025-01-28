<div class="container py-5">
    <!-- Flash Messages -->
    <div class="row">
        <!-- Profile Section -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <!-- Profile Picture -->
                    <div class="mb-3">
                        <?php if ($user['avatar']): ?>
                            <img src="<?= base_url($user['avatar']) ?>" 
                                 class="rounded-circle img-thumbnail" 
                                 style="width: 150px; height: 150px; object-fit: cover;"
                                 alt="<?= esc($user['name']) ?>">
                        <?php else: ?>
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto"
                                 style="width: 150px; height: 150px; font-size: 3rem;">
                                <?= strtoupper(substr($user['name'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- User Info -->
                    <h5 class="mb-1"><?= esc($user['name']) ?></h5>
                    <p class="text-muted mb-3">
                        <i class="fas fa-user-tag me-2"></i><?= ucfirst($user['role']) ?>
                    </p>
                    <?php if ($user['bio']): ?>
                        <p class="mb-3"><?= nl2br(esc($user['bio'])) ?></p>
                    <?php endif; ?>

                    <!-- Member Since -->
                    <div class="text-muted small mb-3">
                        <i class="fas fa-calendar-alt me-2"></i>
                        สมาชิกตั้งแต่ <?= date('d M Y', strtotime($user['created_at'])) ?>
                    </div>

                    <!-- Follow and Message Buttons -->
                    <div class="d-grid gap-2">
                        <?php if ($isFollowing): ?>
                            <form action="<?= base_url('/profile/view/unfollow/' . $user['id']) ?>" method="post">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-user-minus me-2"></i>ยกเลิกการติดตาม
                                </button>
                            </form>
                        <?php else: ?>
                            <form action="<?= base_url('/profile/view/follow/' . $user['id']) ?>" method="post">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-user-plus me-2"></i>ติดตาม
                                </button>
                            </form>
                        <?php endif; ?>
                        <button class="btn btn-outline-primary w-100">
                            <i class="fas fa-envelope me-2"></i>ส่งข้อความ
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="col-lg-8">
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="mb-1"><?= number_format($stats['total_posts']) ?></h3>
                            <div class="text-muted">โพสต์ทั้งหมด</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="mb-1"><?= number_format($stats['total_views']) ?></h3>
                            <div class="text-muted">การเข้าชม</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="mb-1"><?= number_format($stats['total_likes']) ?></h3>
                            <div class="text-muted">ถูกใจ</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Posts -->
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">โพสต์ล่าสุด</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($recent_posts)): ?>
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p class="mb-0">ยังไม่มีโพสต์</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recent_posts as $post): ?>
                            <div class="d-flex mb-3">
                                <?php if ($post['image']): ?>
                                    <img src="<?= base_url($post['image']) ?>" 
                                         class="rounded me-3" 
                                         style="width: 100px; height: 100px; object-fit: cover;"
                                         alt="<?= esc($post['title']) ?>">
                                <?php endif; ?>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="<?= base_url('post/' . $post['slug']) ?>" 
                                           class="text-decoration-none">
                                            <?= esc($post['title']) ?>
                                        </a>
                                    </h6>
                                    <p class="text-muted mb-2">
                                        <?= word_limiter(strip_tags($post['content']), 20) ?>
                                    </p>
                                    <div class="small text-muted">
                                        <i class="fas fa-eye me-1"></i><?= number_format($post['view_count']) ?>
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-heart me-1"></i><?= number_format($post['like_count']) ?>
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-calendar me-1"></i><?= date('d M Y', strtotime($post['created_at'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ยืนยันการลบบัญชี</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        การลบบัญชีไม่สามารถกู้คืนได้ ข้อมูลทั้งหมดของคุณจะถูกลบออกจากระบบ
                    </div>
                    <p>กรุณาพิมพ์ "DELETE" เพื่อยืนยันการลบบัญชี</p>
                    <input type="text" class="form-control" id="deleteConfirm">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <form action="<?= base_url('profile/delete') ?>" method="post">
                        <button type="submit" class="btn btn-danger" id="deleteAccountBtn" disabled>
                            ลบบัญชี
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
