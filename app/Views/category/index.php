<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="fas fa-folder me-2"></i>หมวดหมู่ทั้งหมด
            </h1>

            <!-- Category Grid -->
            <div class="row">
                <?php foreach ($categories as $category): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <?php if (!empty($category['image'])): ?>
                                <img src="<?= base_url($category['image']) ?>" 
                                     class="card-img-top" 
                                     alt="<?= esc($category['name']) ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="<?= base_url('category/' . $category['slug']) ?>"
                                       class="text-decoration-none">
                                        <i class="fas fa-folder me-2"></i><?= esc($category['name']) ?>
                                    </a>
                                </h5>
                                <?php if (!empty($category['description'])): ?>
                                    <p class="card-text text-muted">
                                        <?= esc($category['description']) ?>
                                    </p>
                                <?php endif; ?>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-primary">
                                        <?= number_format($category['post_count'] ?? 0) ?> โพสต์
                                    </span>
                                    <a href="<?= base_url('category/' . $category['slug']) ?>" 
                                       class="btn btn-outline-primary btn-sm">
                                        ดูเพิ่มเติม
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>