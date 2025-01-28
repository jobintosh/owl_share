<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= base_url() ?>">หน้าแรก</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('category') ?>">หมวดหมู่</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <?= esc($category['name']) ?>
                    </li>
                </ol>
            </nav>

            <!-- Category Header -->
            <div class="category-header mb-5">
                <h1 class="mb-3">
                    <i class="fas fa-folder me-2"></i><?= esc($category['name']) ?>
                </h1>
                <?php if (!empty($category['description'])): ?>
                    <p class="lead text-muted"><?= esc($category['description']) ?></p>
                <?php endif; ?>
            </div>

            <?php if (empty($posts)): ?>
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-folder-open fa-3x text-muted"></i>
                    </div>
                    <h3 class="text-muted">ไม่พบเนื้อหาในหมวดหมู่นี้</h3>
                    <p class="text-muted">ยังไม่มีเนื้อหาในหมวดหมู่ <?= esc($category['name']) ?></p>
                    <?php if (session()->get('user_id')): ?>
                        <a href="<?= base_url('share') ?>" class="btn btn-primary mt-3">
                            <i class="fas fa-plus me-2"></i>สร้างเนื้อหาใหม่
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Posts Grid -->
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

                                    <?php if (!empty($post['tags'])): ?>
                                        <div class="content-card-tags mt-3">
                                            <?php foreach (json_decode($post['tags']) as $tag): ?>
                                                <a href="<?= base_url('tag/' . urlencode($tag)) ?>" 
                                                   class="badge bg-light text-dark text-decoration-none me-1">
                                                    #<?= esc($tag) ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.category-header {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 10px;
    margin-bottom: 2rem;
}

.content-card {
    transition: transform 0.2s;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    border-radius: 10px;
}

.content-card:hover {
    transform: translateY(-5px);
}

.content-card-image {
    height: 200px;
    overflow: hidden;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}

.content-card-body {
    padding: 1.5rem;
}
</style>