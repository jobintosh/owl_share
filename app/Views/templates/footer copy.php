
<body class="d-flex flex-column min-vh-100">

    <footer class="py-4 mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>ShareHub</h5>
                    <p>แพลตฟอร์มแบ่งปันความรู้และประสบการณ์</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-decoration-none me-3">เกี่ยวกับเรา</a>
                    <a href="#" class="text-decoration-none me-3">ติดต่อ</a>
                    <a href="#" class="text-decoration-none">นโยบายความเป็นส่วนตัว</a>
                </div>
            </div>
        </div>
    </footer>

    <?php if(current_url() == base_url('share')): ?>
        <script src="<?= base_url('js/share-helpers.js') ?>"></script>
        <script src="<?= base_url('js/share-display.js') ?>"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                ShareHelpers.initEditor();
                ShareHelpers.initWhiteboard();
                ShareHelpers.initGallery();
                ShareHelpers.initTags();
            });
        </script>
    <?php endif; ?>

    <script src="<?= base_url('js/main.js') ?>"></script>

</body>

