<div class="col-md-4 mb-4">
    <div class="content-card">
        <div class="content-card-image">
            <img src="<?= esc($post['image']) ?>" alt="<?= esc($post['title']) ?>" class="img-fluid">
        </div>
        <div class="content-card-body">
            <h5 class="content-card-title"><?= esc($post['title']) ?></h5>
            <p class="content-card-text"><?= esc($post['excerpt']) ?></p>
            
            <div class="content-card-meta">
                <span>
                    <i class="fas fa-user"></i>
                    <?= esc($post['author']) ?>
                </span>
                <span>
                    <i class="fas fa-calendar"></i>
                    <?= date('d M Y', strtotime($post['date'])) ?>
                </span>
            </div>
            
            <div class="content-card-stats">
                <span>
                    <i class="fas fa-eye"></i>
                    <?= number_format($post['views']) ?>
                </span>
                <span>
                    <i class="fas fa-heart"></i>
                    <?= number_format($post['likes']) ?>
                </span>
            </div>
        </div>
    </div>
</div>