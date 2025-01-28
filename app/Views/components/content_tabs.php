<div class="container my-5">
    <!-- status -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 text-center">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-file-alt fa-3x text-primary"></i>
                    </div>
                    <h5 class="card-title">จำนวนโพสต์ทั้งหมด</h5>
                    <p class="fs-4 fw-bold text-dark" data-count-to="<?= $stats['total_posts'] ?>">0</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 text-center">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-comments fa-3x text-success"></i>
                    </div>
                    <h5 class="card-title">จำนวนความคิดเห็น</h5>
                    <p class="fs-4 fw-bold text-dark" data-count-to="<?= $stats['total_comments'] ?>">0</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 text-center">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-users fa-3x text-warning"></i>
                    </div>
                    <h5 class="card-title">จำนวนสมาชิก</h5>
                    <p class="fs-4 fw-bold text-dark" data-count-to="<?= $stats['total_users'] ?>">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Headers -->
    <ul class="nav nav-tabs mb-4" id="contentTabs" role="tablist">
    <?php if (is_array($tabs) && !empty($tabs)): ?>
        <?php foreach ($tabs as $key => $tab): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $key === 'trending' ? 'active' : '' ?>"
                    id="<?= $key ?>-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#<?= $key ?>"
                    type="button"
                    role="tab">
                    <i class="<?= $tab['icon'] ?> me-2"></i>
                    <?= esc($tab['title']) ?>
                </button>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <li class="nav-item">
            <p class="text-muted">ไม่พบข้อมูลแท็บ</p>
        </li>
    <?php endif; ?>
    <!-- โพสล่าสุด -->
    <!-- <li class="nav-item" role="presentation">
        <button class="nav-link" id="recent-tab" data-bs-toggle="tab" data-bs-target="#recent" type="button" role="tab">
            <i class="fas fa-clock me-2"></i>เนื้อหาล่าสุด
        </button>
    </li> -->
</ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="contentTabsContent">
        <?php if (is_array($tabs) && !empty($tabs)): ?>
            <?php foreach ($tabs as $key => $tab): ?>
                <div class="tab-pane fade <?= $key === 'trending' ? 'show active' : '' ?>"
                    id="<?= $key ?>"
                    role="tabpanel">
                    <?php if (empty($tab['posts'])): ?>
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-inbox fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">ยังไม่มีเนื้อหาในหมวดหมู่นี้</h5>
                            <?php if (session()->get('user_id')): ?>
                                <a href="<?= base_url('share') ?>" class="btn btn-primary mt-3">
                                    <i class="fas fa-plus me-2"></i>เริ่มแบ่งปันเนื้อหา
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($tab['posts'] as $post): ?>
                                <div class="col-md-4 mb-4">
                                    <div class="content-card h-100">
                                        <?php
                                        $imagePath = !empty($post['image']) ? base_url($post['image']) : base_url('images/fallback/owl-fallback.png');
                                        ?>
                                        <div class="content-card-image">
                                            <a href="<?= base_url('post/' . $post['slug']) ?>" class="text-decoration-none">
                                                <img src="<?= $imagePath ?>"
                                                    alt="<?= esc($post['title']) ?>"
                                                    class="w-100 h-100 object-fit-cover">
                                            </a>
                                            <?php if (!empty($post['category_name'])): ?>
                                                <span class="category-badge">
                                                    <?= esc($post['category_name']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="content-card-body">
                                            <h5 class="content-card-title">
                                                <a href="<?= base_url('post/' . $post['slug']) ?>"
                                                    class="text-decoration-none text-dark">
                                                    <?= esc($post['title']) ?>
                                                </a>
                                            </h5>

                                            <!-- Popular Tags -->
                                            <div class="content-card-tags mt-2">
                                                <?php if (!empty($post['tags'])): ?>
                                                    <?php
                                                    // ชุดสีที่ต้องการ (สามารถเพิ่มหรือลดได้ตามต้องการ)
                                                    $colors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'dark'];
                                                    ?>
                                                    <?php foreach (explode(',', $post['tags']) as $tag): ?>
                                                        <?php
                                                        $cleanTag = trim($tag, '[]""'); // ลบสัญลักษณ์ [] และ "" ออก
                                                        // เลือกสีโดยใช้แฮชเพื่อให้ได้สีเฉพาะแท็ก
                                                        $colorClass = $colors[crc32($cleanTag) % count($colors)];
                                                        // สร้าง URL สำหรับแท็ก
                                                        $tagUrl = base_url('tag/' . urlencode($cleanTag));
                                                        ?>
                                                        <a href="<?= esc($tagUrl) ?>" class="text-decoration-none">
                                                            <span class="badge bg-<?= esc($colorClass) ?>">#<?= esc($cleanTag) ?></span>
                                                        </a>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Content Status -->
                                            <div class="content-card-stats mt-3">
                                                <span title="จำนวนการดู">
                                                    <i class="far fa-eye me-1"></i>
                                                    <?= number_format($post['view_count']) ?>
                                                </span>
                                                <span title="จำนวนความคิดเห็น">
                                                    <i class="far fa-comment me-1"></i>
                                                    <?= number_format($post['comment_count']) ?>
                                                </span>
                                            </div>
                                            <!-- Divider -->
                                            <hr class="my-3">
                                            <!-- Author Info -->
                                            <div class="content-card-meta mt-3">
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($post['author_avatar'])): ?>
                                                        <img src="<?= base_url($post['author_avatar']) ?>"
                                                            class="rounded-circle me-2"
                                                            width="24" height="24"
                                                            alt="<?= esc($post['author_name']) ?>">
                                                    <?php else: ?>
                                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                                            style="width: 24px; height: 24px;">
                                                            <?= strtoupper(substr($post['author_name'], 0, 1)) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <a href="<?= site_url('profile/view/' . $post['author_id']) ?>" class="small text-muted">
                                                        <?= esc($post['author_name']) ?>
                                                    </a>
                                                </div>
                                                <div class="small text-muted mt-1">
                                                    <i class="far fa-calendar me-1"></i>
                                                    <?= date('d M Y', strtotime($post['created_at'])) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">ไม่พบข้อมูลแท็บ</p>
        <?php endif; ?>

        <!-- Content for Recent Tab -->
        <div class="tab-pane fade" id="recent" role="tabpanel">
            <?php if (empty($recentPosts)): ?>
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-inbox fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted">ยังไม่มีเนื้อหาล่าสุด</h5>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($recentPosts as $post): ?>
                        <div class="col-md-4 mb-4">
                            <div class="content-card h-100">
                                <?php
                                $imagePath = !empty($post['image']) ? base_url($post['image']) : base_url('images/fallback/owl-fallback.png');
                                ?>
                                <div class="content-card-image">
                                    <a href="<?= base_url('post/' . $post['slug']) ?>" class="text-decoration-none">
                                        <img src="<?= $imagePath ?>"
                                            alt="<?= esc($post['title']) ?>"
                                            class="w-100 h-100 object-fit-cover">
                                    </a>
                                    <?php if (!empty($post['category_name'])): ?>
                                        <span class="category-badge">
                                            <?= esc($post['category_name']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="content-card-body">
                                    <h5 class="content-card-title">
                                        <a href="<?= base_url('post/' . $post['slug']) ?>"
                                            class="text-decoration-none text-dark">
                                            <?= esc($post['title']) ?>
                                        </a>
                                    </h5>

                                    <!-- Popular Tags -->
                                    <div class="content-card-tags mt-2">
                                        <?php if (!empty($post['tags'])): ?>
                                            <?php
                                            // ชุดสีที่ต้องการ (สามารถเพิ่มหรือลดได้ตามต้องการ)
                                            $colors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'dark'];
                                            ?>
                                            <?php foreach (explode(',', $post['tags']) as $tag): ?>
                                                <?php
                                                $cleanTag = trim($tag, '[]""'); // ลบสัญลักษณ์ [] และ "" ออก
                                                // เลือกสีโดยใช้แฮชเพื่อให้ได้สีเฉพาะแท็ก
                                                $colorClass = $colors[crc32($cleanTag) % count($colors)];
                                                // สร้าง URL สำหรับแท็ก
                                                $tagUrl = base_url('tag/' . urlencode($cleanTag));
                                                ?>
                                                <a href="<?= esc($tagUrl) ?>" class="text-decoration-none">
                                                    <span class="badge bg-<?= esc($colorClass) ?>">#<?= esc($cleanTag) ?></span>
                                                </a>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Content Status -->
                                    <div class="content-card-stats mt-3">
                                        <span title="จำนวนการดู">
                                            <i class="far fa-eye me-1"></i>
                                            <?= number_format($post['view_count']) ?>
                                        </span>
                                        <span title="จำนวนความคิดเห็น">
                                            <i class="far fa-comment me-1"></i>
                                            <?= number_format($post['comment_count']) ?>
                                        </span>
                                    </div>
                                    <!-- Divider -->
                                    <hr class="my-3">
                                    <!-- Author Info -->
                                    <div class="content-card-meta mt-3">
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($post['author_avatar'])): ?>
                                                <img src="<?= base_url($post['author_avatar']) ?>"
                                                    class="rounded-circle me-2"
                                                    width="24" height="24"
                                                    alt="<?= esc($post['author_name']) ?>">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                                    style="width: 24px; height: 24px;">
                                                    <?= strtoupper(substr($post['author_name'], 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>
                                            <a href="<?= site_url('profile/view/' . $post['author_id']) ?>" class="small text-muted">
                                                <?= esc($post['author_name']) ?>
                                            </a>
                                        </div>
                                        <div class="small text-muted mt-1">
                                            <i class="far fa-calendar me-1"></i>
                                            <?= date('d M Y', strtotime($post['created_at'])) ?>
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

<script>
    const tabs = document.querySelectorAll('.nav-link');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Remove 'active' class from all tabs
            tabs.forEach(t => t.classList.remove('active'));
            // Add 'active' class to the clicked tab
            tab.classList.add('active');
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const tabButtons = document.querySelectorAll('#contentTabs button');
        tabButtons.forEach(button => {
            button.addEventListener('click', event => {
                const target = document.querySelector(button.dataset.bsTarget);
                if (target) {
                    document.querySelectorAll('.tab-pane').forEach(pane => {
                        pane.classList.remove('show', 'active');
                    });
                    target.classList.add('show', 'active');
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const counters = document.querySelectorAll('[data-count-to]');

        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-count-to');
                const current = +counter.innerText;
                const increment = target / 100;

                if (current < target) {
                    counter.innerText = Math.ceil(current + increment);
                    setTimeout(updateCount, 20);
                } else {
                    counter.innerText = target;
                }
            };

            updateCount();
        });
    });
</script>