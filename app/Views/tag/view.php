<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= base_url() ?>">หน้าแรก</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('tag') ?>">แท็ก</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <?= esc($tag['name']) ?>
                    </li>
                </ol>
            </nav>

            <div class="tag-header mb-5">
                <h1 class="mb-3">#<?= esc($tag['name']) ?></h1>
                <?php if ($tag['description']): ?>
                    <p class="lead text-muted"><?= esc($tag['description']) ?></p>
                <?php endif; ?>
            </div>

            <?php if (empty($posts)): ?>
                <!-- แสดงเมื่อไม่พบโพสต์ -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-search fa-3x text-muted"></i>
                    </div>
                    <h3 class="text-muted">ไม่พบเนื้อหาสำหรับแท็กนี้</h3>
                    <p class="text-muted">ยังไม่มีเนื้อหาที่ใช้แท็ก #<?= esc($tag['name']) ?></p>
                    <?php if (session()->get('user_id')): ?>
                        <a href="<?= base_url('share') ?>" class="btn btn-primary mt-3">
                            <i class="fas fa-plus me-2"></i>สร้างเนื้อหาใหม่
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- แสดงเมื่อพบโพสต์ -->
                <div class="row">
                    <?php foreach ($posts as $post): ?>
                        <div class="col-md-4 mb-4">
                            <div class="content-card h-100">
                                <?php if (!empty($post['image'])): ?>
                                    <div class="content-card-image">
                                        <img src="<?= base_url($post['image']) ?>" 
                                             alt="<?= esc($post['title']) ?>"
                                             class="w-100 h-100 object-fit-cover">
                                    </div>
                                <?php endif; ?>

                                <div class="content-card-body">
                                    <h5 class="content-card-title">
                                        <a href="<?= base_url('post/' . $post['slug']) ?>"
                                           class="text-decoration-none text-dark">
                                            <?= esc($post['title']) ?>
                                        </a>
                                    </h5>

                                    <p class="content-card-text text-muted">
                                        <?= word_limiter(strip_tags($post['content']), 20) ?>
                                    </p>

                                    <div class="content-card-meta">
                                        <div class="d-flex gap-3 text-muted">
                                            <span>
                                                <i class="far fa-eye me-1"></i>
                                                <?= number_format($post['view_count']) ?>
                                            </span>
                                            <span>
                                                <i class="far fa-heart me-1"></i>
                                                <?= number_format($post['like_count']) ?>
                                            </span>
                                            <span>
                                                <i class="far fa-comment me-1"></i>
                                                <?= number_format($post['comment_count']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>