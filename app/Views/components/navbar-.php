<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url() ?>">ShareHub</a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?= current_url() == base_url() ? 'active' : '' ?>" href="<?= base_url() ?>">
                        หน้าแรก
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= current_url() == base_url('share') ? 'active' : '' ?>" href="<?= base_url('share') ?>">
                        แบ่งปันข้อมูล
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">โปรไฟล์</a>
                </li>
            </ul>
        </div>
    </div>
</nav>