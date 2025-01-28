<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container my-5">
    <div class="row">
        <!-- หน้าฟอร์มแบ่งปันข้อมูล -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">แบ่งปันข้อมูล</h5>

                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs mb-3" id="contentTypeTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#text" type="button" role="tab">
                                <i class="fas fa-pen"></i> เขียนข้อความ
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#whiteboard" type="button" role="tab">
                                <i class="fas fa-paint-brush"></i> วาดภาพ
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#gallery" type="button" role="tab">
                                <i class="fas fa-images"></i> แกลเลอรี่
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <form id="shareForm" onsubmit="return handleSubmit(event)">
                        <div class="mb-3">
                            <label for="titleInput">หัวข้อ</label>
                            <input type="text" class="form-control" id="titleInput" name="title" required minlength="5">
                        </div>

                        <div class="tab-content" id="contentTypeTabsContent">
                            <!-- Text Tab -->
                            <div class="tab-pane fade show active" id="text" role="tabpanel">
                                <div class="mb-3">
                                    <textarea id="editor" name="content"></textarea>
                                </div>
                            </div>

                            <!-- Whiteboard Tab -->
                            <div class="tab-pane fade" id="whiteboard" role="tabpanel">
                                <div class="mb-3">
                                    <div class="whiteboard-container">
                                        <div class="whiteboard-tools">
                                            <button type="button" id="penTool" class="btn btn-outline-primary active">
                                                <i class="fas fa-pen"></i> ปากกา
                                            </button>
                                            <button type="button" id="eraserTool" class="btn btn-outline-primary">
                                                <i class="fas fa-eraser"></i> ยางลบ
                                            </button>
                                            <input type="color" id="colorPicker" class="form-control" value="#000000">
                                            <select id="brushSize" class="form-select">
                                                <option value="1">1px</option>
                                                <option value="3">3px</option>
                                                <option value="5" selected>5px</option>
                                                <option value="8">8px</option>
                                                <option value="10">10px</option>
                                                <option value="15">15px</option>
                                            </select>
                                            <button type="button" id="clearCanvas" class="btn btn-outline-danger">
                                                <i class="fas fa-trash"></i> ล้างกระดาน
                                            </button>
                                        </div>
                                        <canvas id="whiteboardCanvas"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Gallery Tab -->
                            <div class="tab-pane fade" id="gallery" role="tabpanel">
                                <div class="mb-3">
                                    <div id="imageDropzone" class="dropzone-container">
                                        <input type="file" id="imageUpload" multiple accept="image/*" style="display: none;">
                                        <div class="dropzone-message">
                                            <i class="fas fa-cloud-upload-alt mb-2"></i>
                                            <p>ลากไฟล์มาวางที่นี่ หรือคลิกเพื่อเลือกไฟล์</p>
                                            <p class="small">รองรับไฟล์ภาพ JPG, PNG, GIF</p>
                                        </div>
                                    </div>
                                    <div id="imagePreview" class="image-preview"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Category -->
                            <div class="col-md-6">
                                <label for="categorySelect">หมวดหมู่</label>
                                <select class="form-select" id="categorySelect" name="category" required>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Tags -->
                            <div class="col-md-6">
                                <label for="tagsInput">แท็ก</label>
                                <input type="text" class="form-control" id="tagsInput" 
                                       placeholder="พิมพ์แท็กแล้วกด Enter">
                                <div id="tagsContainer" class="mt-2"></div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-share"></i> แบ่งปัน
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- โพสต์ล่าสุด -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">โพสต์ล่าสุด</h5>
                    <div id="sharedContent" class="shared-items">
                        <?php foreach ($recent_posts as $post): ?>
                            <div class="shared-item">
                                <div class="title"><?= esc($post['title']) ?></div>
                                <div class="content">
                                    <?php if ($post['type'] === 'text'): ?>
                                        <?= $post['content'] ?>
                                    <?php elseif ($post['type'] === 'whiteboard'): ?>
                                        <img src="<?= base_url($post['content']) ?>" alt="Whiteboard" class="img-fluid">
                                    <?php elseif ($post['type'] === 'gallery'): ?>
                                        <div class="gallery-grid">
                                            <?php foreach (json_decode($post['content'], true) as $image): ?>
                                                <div class="gallery-item">
                                                    <img src="<?= base_url($image) ?>" alt="Gallery Image" loading="lazy">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="metadata">
                                    <span class="category-badge">
                                        <?= $post['category_name'] ?>
                                    </span>
                                    <?php if (!empty($post['tags'])): ?>
                                        <?php foreach (json_decode($post['tags'], true) as $tag): ?>
                                            <span class="tag-badge"><?= esc($tag) ?></span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <span class="ms-3">
                                        <i class="fas fa-user"></i> <?= esc($post['author_name']) ?>
                                    </span>
                                    <span class="ms-3">
                                        <i class="fas fa-clock"></i> 
                                        <?= date('d M Y H:i', strtotime($post['created_at'])) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.tiny.cloud/1/your-api-key/tinymce/5/tinymce.min.js"></script>
<script src="<?= base_url('js/script.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize components
    initWhiteboard();
    initGallery();
    
    // Form submission handler
    const shareForm = document.getElementById('shareForm');
    shareForm?.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData();
        const activeTab = document.querySelector('#contentTypeTabs .nav-link.active');
        const contentType = activeTab.getAttribute('data-bs-target').replace('#', '');

        formData.append('title', document.getElementById('titleInput').value);
        formData.append('category', document.getElementById('categorySelect').value);
        formData.append('content_type', contentType);
        formData.append('tags', JSON.stringify(currentTags));

        switch (contentType) {
            case 'text':
                formData.append('content', tinymce.get('editor').getContent());
                break;

            case 'whiteboard':
                const canvas = document.getElementById('whiteboardCanvas');
                const imageData = canvas.toDataURL('image/png');
                formData.append('whiteboard_data', imageData);
                break;

            case 'gallery':
                const preview = document.getElementById('imagePreview');
                const images = preview.getElementsByTagName('img');
                Array.from(images).forEach((img, index) => {
                    fetch(img.src)
                        .then(res => res.blob())
                        .then(blob => formData.append(`gallery[]`, blob, `image${index}.png`));
                });
                break;
        }

        try {
            const response = await fetch('<?= base_url('share/create') ?>', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Show success message
                alert(result.message);
                // Reset form
                shareForm.reset();
                // Reload page or update content
                window.location.reload();
            } else {
                alert(result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
        }
    });
});
</script>
<?= $this->endSection() ?>