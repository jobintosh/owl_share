<?php
// views/post/sidebar.php
?>

<aside class="mb-4">
    <!-- Related Posts -->
    <?php if (!empty($related_posts)): ?>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">โพสต์ที่เกี่ยวข้อง</h5>
            <div class="mt-3">
                <?php foreach ($related_posts as $related): ?>
                <div class="d-flex mb-3">
                    <?php if ($related['image']): ?>
                    <div class="flex-shrink-0 me-3">
                        <img src="<?= esc($related['image']) ?>" 
                             alt="<?= esc($related['title']) ?>" 
                             class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                    </div>
                    <?php endif; ?>
                    <div>
                        <a href="<?= site_url('post/' . $related['slug']) ?>" 
                           class="text-dark text-decoration-none fw-bold">
                            <?= esc($related['title']) ?>
                        </a>
                        <!-- มีปัญหารูปไม่โหลด -->
                        <div class="mt-2 d-flex align-items-center text-muted small">
                        <img src="<?= base_url('/' . (isset($post['author_avatar']) && !empty($post['author_avatar']) ? esc($post['author_avatar']) : '/avatars/default-avatar.png')) ?>"
                        alt="<?= esc($related['author_name']) ?>" 
                                 class="rounded-circle me-2" style="width: 20px; height: 20px;">
                            <?= esc($related['author_name']) ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Categories -->
    <?php if (!empty($categories)): ?>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">หมวดหมู่</h5>
            <ul class="list-unstyled mt-3">
                <?php foreach ($categories as $category): ?>
                <li class="d-flex justify-content-between align-items-center mb-2">
                    <a href="<?= site_url('category/' . $category['slug']) ?>" 
                       class="text-dark text-decoration-none">
                        <?php if ($category['icon']): ?>
                        <i class="<?= $category['icon'] ?> me-2"></i>
                        <?php endif; ?>
                        <?= esc($category['name']) ?>
                    </a>
                    <span class="text-muted small"><?= number_format($category['post_count']) ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <!-- Trending Tags -->
    <?php if (!empty($trending_tags)): ?>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">แท็กยอดนิยม</h5>
            <div class="mt-3">
                <?php foreach ($trending_tags as $tag): ?>
                <a href="<?= site_url('tag/' . urlencode($tag['name'])) ?>" 
                   class="badge bg-light text-dark me-2 mb-2 text-decoration-none">
                    #<?= esc($tag['name']) ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</aside>
