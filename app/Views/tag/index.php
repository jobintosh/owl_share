
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="fas fa-tags me-2"></i>แท็กทั้งหมด
            </h1>

            <!-- Tag Cloud -->
            <div class="card mb-5">
                <div class="card-body">
                    <?php foreach ($tags as $tag): ?>
                        <a href="<?= base_url('tag/' . $tag['slug']) ?>" 
                           class="btn btn-outline-primary btn-sm m-1"
                           style="font-size: <?= max(0.8, min(2.0, 0.8 + ($tag['post_count'] / 50))) ?>rem">
                            #<?= esc($tag['name']) ?>
                            <span class="badge bg-primary ms-1">
                                <?= number_format($tag['post_count']) ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Popular Tags -->
            <h2 class="h4 mb-3">
                <i class="fas fa-star me-2"></i>แท็กยอดนิยม
            </h2>
            <div class="row">
                <?php foreach ($popularTags as $tag): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="<?= base_url('tag/' . $tag['slug']) ?>"
                                       class="text-decoration-none">
                                        #<?= esc($tag['name']) ?>
                                    </a>
                                </h5>
                                <p class="card-text text-muted">
                                    <?= esc($tag['description'] ?? 'ไม่มีคำอธิบาย') ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-primary">
                                        <?= number_format($tag['post_count']) ?> โพสต์
                                    </span>
                                    <small class="text-muted">
                                        สร้างเมื่อ <?= date('d M Y', strtotime($tag['created_at'])) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>