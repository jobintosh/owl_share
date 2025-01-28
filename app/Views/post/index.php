<div class="container my-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <!-- Search -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="<?= base_url('post') ?>" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search"
                                placeholder="ค้นหาโพสต์"
                                value="<?= $current_search ?? '' ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Categories -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-folder me-2"></i>หมวดหมู่
                    </h5>
                </div>

                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($categories as $category): ?>
                            <li class="mb-2">
                                <a href="<?= base_url('category/' . $category['slug']) ?>"
                                    class="text-decoration-none <?= ($current_category == $category['slug']) ? 'text-primary fw-bold' : 'text-dark' ?>">
                                    <?php if ($category['icon']): ?>
                                        <i class="<?= $category['icon'] ?> me-2"></i>
                                    <?php endif; ?>
                                    <?= esc($category['name']) ?>
                                    <span class="badge bg-secondary float-end">
                                        <?= $category['post_count'] ?>
                                    </span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Trending Tags -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tags me-2"></i>แท็กยอดนิยม
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($trending_tags as $tag): ?>
                            <a href="<?= base_url('post?tag=' . urlencode($tag['name'])) ?>"
                                class="text-decoration-none">
                                <span class="badge <?= ($current_tag == $tag['name']) ? 'bg-primary' : 'bg-secondary' ?>">
                                    <?= esc($tag['name']) ?>
                                    <span class="badge bg-light text-dark ms-1">
                                        <?= $tag['post_count'] ?>
                                    </span>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Post List -->
        <div class="col-lg-9">
            <!-- Sort Options -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">
                    <?php if ($current_search): ?>
                        ผลการค้นหา: "<?= esc($current_search) ?>"
                    <?php elseif ($current_category): ?>
                        หมวดหมู่: <?= esc($categories[array_search($current_category, array_column($categories, 'id'))]['name']) ?>
                    <?php elseif ($current_tag): ?>
                        แท็ก: <?= esc($current_tag) ?>
                    <?php else: ?>
                        โพสต์ทั้งหมด
                    <?php endif; ?>
                </h4>

                <!-- <div class="btn-group">
                    <a href="<?= current_url() ?>?sort=latest" 
                       class="btn <?= ($current_sort == 'latest') ? 'btn-primary' : 'btn-outline-primary' ?>">
                        <i class="fas fa-clock"></i> ล่าสุด
                    </a>
                    <a href="<?= current_url() ?>?sort=popular" 
                       class="btn <?= ($current_sort == 'popular') ? 'btn-primary' : 'btn-outline-primary' ?>">
                        <i class="fas fa-fire"></i> ยอดนิยม
                    </a>
                    <a href="<?= current_url() ?>?sort=comments" 
                       class="btn <?= ($current_sort == 'comments') ? 'btn-primary' : 'btn-outline-primary' ?>">
                        <i class="fas fa-comments"></i> ความคิดเห็น
                    </a>
                </div>
            </div> -->

                <div class="btn-group">
                    <a href="<?= base_url('post') ?>?sort=latest"
                        class="btn <?= ($current_sort == 'latest') ? 'btn-primary' : 'btn-outline-primary' ?>">
                        <i class="fas fa-clock"></i> ล่าสุด
                    </a>
                    <a href="<?= base_url('post') ?>?sort=popular"
                        class="btn <?= ($current_sort == 'popular') ? 'btn-primary' : 'btn-outline-primary' ?>">
                        <i class="fas fa-fire"></i> ยอดนิยม
                    </a>
                    <a href="<?= base_url('post') ?>?sort=comments"
                        class="btn <?= ($current_sort == 'comments') ? 'btn-primary' : 'btn-outline-primary' ?>">
                        <i class="fas fa-comments"></i> ความคิดเห็น
                    </a>
                </div>
            </div>


            <!-- Posts -->
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <!-- Post Header -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center">
                                    <img src="<?= base_url($post['author_avatar'] ?? 'images/default-avatar.png') ?>"
                                        class="rounded-circle me-2" style="width: 40px; height: 40px;">
                                    <div>
                                        <h5 class="card-title mb-1">
                                            <a href="<?= base_url('post/' . $post['slug']) ?>"
                                                class="text-decoration-none">
                                                <?= esc($post['title']) ?>
                                            </a>
                                        </h5>
                                        <small class="text-muted">
                                            โดย <?= esc($post['author_name']) ?> • <?= time_ago($post['created_at']) ?>
                                        </small>
                                    </div>
                                </div>

                                <?php if (session()->get('user_id') == $post['author_id'] || session()->get('user_role') == 'admin'): ?>
                                    <div class="dropdown">
                                        <button class="btn btn-link text-muted" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="<?= base_url('post/edit/' . $post['id']) ?>">
                                                    <i class="fas fa-edit me-2"></i>แก้ไข
                                                </a>
                                            </li>
                                            <li>
                                                <button class="dropdown-item text-danger"
                                                    onclick="confirmDelete(<?= $post['id'] ?>)">
                                                    <i class="fas fa-trash me-2"></i>ลบ
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Post Content -->
                            <div class="post-content mb-3">
                                <?php if ($post['type'] === 'text'): ?>
                                    <?= character_limiter(strip_tags($post['content']), 200) ?>

                                <?php elseif ($post['type'] === 'whiteboard'): ?>
                                    <img src="<?= base_url($post['content']) ?>"
                                        alt="Whiteboard" class="img-fluid rounded">

                                <?php elseif ($post['type'] === 'gallery'): ?>
                                    <div class="row g-2">
                                        <?php foreach (array_slice(json_decode($post['content'], true), 0, 4) as $index => $image): ?>
                                            <div class="col-3">
                                                <img src="<?= base_url($image) ?>"
                                                    alt="Gallery Image"
                                                    class="img-fluid rounded">
                                                <?php if ($index === 3 && count(json_decode($post['content'], true)) > 4): ?>
                                                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 rounded d-flex align-items-center justify-content-center text-white">
                                                        +<?= count(json_decode($post['content'], true)) - 4 ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Post Footer -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-primary me-2">
                                        <i class="fas fa-folder me-1"></i><?= $post['category_name'] ?>
                                    </span>
                                    <?php if (!empty($post['tags'])): ?>
                                        <?php foreach (json_decode($post['tags'], true) as $tag): ?>
                                            <span class="badge bg-secondary me-1">
                                                <i class="fas fa-tag me-1"></i><?= esc($tag) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>

                                <div>
                                    <button class="btn btn-sm <?= $post['liked'] ? 'btn-primary' : 'btn-outline-primary' ?>"
                                        onclick="toggleLike(<?= $post['id'] ?>, this)">
                                        <i class="fas fa-heart"></i>
                                        <span class="like-count"><?= number_format($post['like_count']) ?></span>
                                    </button>
                                    <a href="<?= base_url('post/' . $post['slug']) ?>#comments"
                                        class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="fas fa-comment"></i>
                                        <span class="comment-count"><?= number_format($post['comment_count']) ?></span>
                                    </a>
                                    <button class="btn btn-sm btn-outline-primary ms-2" data-bs-toggle="modal" data-bs-target="#shareModal">
    <i class="fas fa-share"></i>
</button>

<!-- Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareModalLabel">แชร์โพสต์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-grid gap-2">
                    <!-- Share to Facebook -->
                    <button class="btn btn-outline-primary" onclick="shareOnFacebook()">
                        <i class="fab fa-facebook me-2"></i>แชร์ไปยัง Facebook
                    </button>

                    <!-- Share to Twitter -->
                    <button class="btn btn-outline-info" onclick="shareOnTwitter()">
                        <i class="fab fa-twitter me-2"></i>แชร์ไปยัง Twitter
                    </button>

                    <!-- Share to Line -->
                    <button class="btn btn-outline-success" onclick="shareOnLine()">
                        <i class="fab fa-line me-2"></i>แชร์ไปยัง Line
                    </button>

                    <!-- Copy Link -->
                    <button class="btn btn-outline-secondary" onclick="copyLink()">
                        <i class="fas fa-copy me-2"></i>คัดลอกลิงก์
                    </button>
                </div>

                <hr>

                <div class="input-group">
                    <input type="text" id="shareUrl" class="form-control" value="<?= base_url('post/' . $post['id']) ?>" readonly>
                    <button class="btn btn-outline-secondary" onclick="copyLink()">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Pagination -->
                <?= $pager->links() ?>

            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5>ไม่พบโพสต์</h5>
                    <p class="text-muted">ลองค้นหาด้วยคำค้นอื่น หรือดูโพสต์ทั้งหมด</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ยืนยันการลบ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>คุณต้องการลบโพสต์นี้ใช่หรือไม่?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-danger" onclick="deletePost()">ลบ</button>
            </div>
        </div>
    </div>
</div>

<!-- Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">แชร์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" onclick="shareFacebook()">
                        <i class="fab fa-facebook me-2"></i>แชร์ไปยัง Facebook
                    </button>
                    <button class="btn btn-outline-info" onclick="shareTwitter()">
                        <i class="fab fa-twitter me-2"></i>แชร์ไปยัง Twitter
                    </button>
                    <button class="btn btn-outline-success" onclick="shareLine()">
                        <i class="fab fa-line me-2"></i>แชร์ไปยัง Line
                    </button>
                </div>
                <hr>
                <div class="input-group">
                    <input type="text" id="shareUrl" class="form-control" readonly>
                    <button class="btn btn-outline-secondary" onclick="copyShareUrl()">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Post Scripts -->
<script>
    function confirmDelete(postId) {
        // เปิด modal ยืนยันการลบ
        $('#deleteModal').modal('show');
        // เก็บรหัสโพสต์ที่ต้องการลบ
        $('#deleteModal').data('postId', postId);
    }

    function deletePost() {
        var postId = $('#deleteModal').data('postId');
        // ส่งคำขอลบโพสต์ไปยัง Controller
        window.location.href = "<?= base_url('post/delete') ?>/" + postId;
    }

    function shareOnFacebook() {
        var shareUrl = document.getElementById('shareUrl').value;
        var facebookUrl = "https://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(shareUrl);
        openSharePopup(facebookUrl);
    }

    // ฟังก์ชันแชร์ไปยัง Twitter
    function shareOnTwitter() {
        var shareUrl = document.getElementById('shareUrl').value;
        var twitterUrl = "https://twitter.com/intent/tweet?url=" + encodeURIComponent(shareUrl);
        openSharePopup(twitterUrl);
    }

    // ฟังก์ชันแชร์ไปยัง Line
    function shareOnLine() {
        var shareUrl = document.getElementById('shareUrl').value;
        var lineUrl = "https://social-plugins.line.me/lineit/share?url=" + encodeURIComponent(shareUrl);
        openSharePopup(lineUrl);
    }

    // ฟังก์ชันเปิดหน้าต่างแชร์
    function openSharePopup(url) {
        var windowOptions = "width=600,height=400,left=" + (window.innerWidth - 600) / 2 + ",top=" + (window.innerHeight - 400) / 2 + ",menubar=no,toolbar=no,resizable=yes";
        window.open(url, '_blank', windowOptions);
    }

    // ฟังก์ชันคัดลอกลิงก์
    function copyLink() {
        var linkInput = document.getElementById('shareUrl');
        linkInput.select();
        linkInput.setSelectionRange(0, 99999); // For mobile devices
        document.execCommand('copy');

        // แสดงข้อความว่าได้คัดลอกลิงก์แล้ว
        alert('คัดลอกลิงก์แล้ว!');
    }
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>