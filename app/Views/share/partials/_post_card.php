<?php
/**
 * Post Card Partial View
 */
?>
<div class="post-card" id="post-<?= $post['id'] ?>">
    <!-- Post Header -->
    <div class="post-header">
        <div class="d-flex justify-content-between align-items-start">
            <div class="d-flex align-items-center">
                <img src="<?= $post['author_avatar'] ?? base_url('images/default-avatar.png') ?>"
                     class="avatar me-2" alt="<?= esc($post['author_name']) ?>">
                <div>
                    <h5 class="post-title mb-1"><?= esc($post['title']) ?></h5>
                    <div class="post-meta">
                        <span class="author-name">
                            <i class="fas fa-user"></i> <?= esc($post['author_name']) ?>
                        </span>
                        <span class="post-date ms-2">
                            <i class="far fa-clock"></i> <?= time_ago($post['created_at']) ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <?php if (session()->get('user_id') == $post['author_id']): ?>
                <div class="dropdown">
                    <button class="btn btn-link text-muted" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="<?= base_url('share/edit/' . $post['id']) ?>">
                                <i class="fas fa-edit"></i> แก้ไข
                            </a>
                        </li>
                        <li>
                            <button class="dropdown-item text-danger" 
                                    onclick="PostCard.delete(<?= $post['id'] ?>)">
                                <i class="fas fa-trash"></i> ลบ
                            </button>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Post Content -->
    <div class="post-content mt-3">
        <?php if ($post['type'] === 'text'): ?>
            <div class="text-content">
                <?= $post['content'] ?>
            </div>

        <?php elseif ($post['type'] === 'whiteboard'): ?>
            <div class="whiteboard-content">
                <img src="<?= base_url($post['content']) ?>" 
                     alt="Whiteboard" class="img-fluid rounded cursor-pointer"
                     onclick="PostCard.viewImage(this.src)">
            </div>

        <?php elseif ($post['type'] === 'gallery'): ?>
            <div class="gallery-content">
                <div class="gallery-grid">
                    <?php foreach (json_decode($post['content'], true) as $index => $image): ?>
                        <div class="gallery-item">
                            <img src="<?= base_url($image) ?>" 
                                 alt="Gallery Image"
                                 loading="lazy"
                                 onclick="PostCard.viewImage(this.src)"
                                 class="cursor-pointer">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Post Footer -->
    <div class="post-footer mt-3">
        <!-- Categories and Tags -->
        <div class="mb-2">
            <a href="<?= base_url('category/' . $post['category_slug']) ?>" 
               class="category-badge text-decoration-none">
                <i class="fas fa-folder"></i> <?= esc($post['category_name']) ?>
            </a>
            
            <?php if (!empty($post['tags'])): ?>
                <?php foreach (json_decode($post['tags'], true) as $tag): ?>
                    <a href="<?= base_url('search?tag=' . urlencode($tag)) ?>" 
                       class="tag-badge text-decoration-none">
                        <i class="fas fa-tag"></i> <?= esc($tag) ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Action Buttons -->
        <div class="post-actions">
            <button type="button" 
                    class="btn btn-sm <?= $post['liked'] ? 'btn-primary' : 'btn-outline-primary' ?>"
                    onclick="PostCard.toggleLike(<?= $post['id'] ?>)">
                <i class="fas fa-heart"></i>
                <span class="like-count"><?= number_format($post['like_count']) ?></span>
            </button>

            <button type="button" 
                    class="btn btn-sm btn-outline-primary ms-2"
                    onclick="PostCard.toggleComments(<?= $post['id'] ?>)">
                <i class="fas fa-comment"></i>
                <span class="comment-count"><?= number_format($post['comment_count']) ?></span>
            </button>

            <button type="button" 
                    class="btn btn-sm btn-outline-primary ms-2"
                    onclick="PostCard.share(<?= $post['id'] ?>)">
                <i class="fas fa-share"></i> แชร์
            </button>
        </div>

        <!-- Comments Section -->
        <div id="comments-<?= $post['id'] ?>" class="comments-section mt-3" style="display: none;">
            <!-- Comments will be loaded here -->
        </div>
    </div>
</div>

<style>
.post-card {
    background: white;
    border-radius: 0.5rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: transform 0.2s ease;
}

.post-card:hover {
    transform: translateY(-2px);
}

.avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
}

.post-title {
    font-size: 1.25rem;
    color: #2d3436;
}

.post-meta {
    font-size: 0.875rem;
    color: #636e72;
}

.post-content {
    color: #2d3436;
    line-height: 1.6;
}

.post-content img {
    max-width: 100%;
    height: auto;
    border-radius: 0.375rem;
}

.post-actions .btn {
    min-width: 80px;
}

.cursor-pointer {
    cursor: pointer;
}
</style>