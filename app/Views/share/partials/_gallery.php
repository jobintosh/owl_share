<?php
/**
 * Gallery Upload Partial
 */
?>
<div class="gallery-section">
    <!-- Dropzone Area -->
    <div id="imageDropzone" class="dropzone-container">
        <input type="file" id="imageUpload" multiple accept="image/*" style="display: none;">
        <div class="dropzone-message text-center py-5">
            <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
            <p class="mb-2">ลากไฟล์มาวางที่นี่ หรือคลิกเพื่อเลือกไฟล์</p>
            <p class="small text-muted">
                รองรับไฟล์ภาพ JPG, PNG, GIF (สูงสุด 5MB ต่อไฟล์)
            </p>
        </div>
    </div>

    <!-- Image Preview -->
    <div id="imagePreview" class="image-preview mt-3"></div>

    <!-- Image Sorting Controls -->
    <div class="sort-controls mt-3 text-end d-none">
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="GalleryHandler.sortByName()">
            <i class="fas fa-sort-alpha-down"></i> เรียงตามชื่อ
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="GalleryHandler.sortByDate()">
            <i class="fas fa-sort-numeric-down"></i> เรียงตามวันที่
        </button>
    </div>
</div>

<style>
.dropzone-container {
    border: 2px dashed #dee2e6;
    border-radius: 0.375rem;
    background-color: #f8f9fa;
    cursor: pointer;
    transition: all 0.3s ease;
}

.dropzone-container:hover,
.dropzone-container.dragover {
    background-color: #e9ecef;
    border-color: #0d6efd;
}

.dropzone-message i {
    display: block;
    margin-bottom: 1rem;
}

.image-preview {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
}

.preview-item {
    position: relative;
    padding-top: 100%;
    border-radius: 0.375rem;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.preview-item img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.preview-item:hover img {
    transform: scale(1.05);
}

.preview-item .preview-controls {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.preview-item:hover .preview-controls {
    opacity: 1;
}

.preview-controls button {
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    border: none;
    background: white;
    color: #333;
    cursor: pointer;
    transition: all 0.2s ease;
}

.preview-controls button:hover {
    background: #0d6efd;
    color: white;
}

.dragging {
    opacity: 0.5;
    transform: scale(0.95);
}

@media (max-width: 768px) {
    .image-preview {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    }
}
</style>

<script>
const GalleryHandler = {
    files: [],
    previewContainer: null,
    dropzone: null,
    maxFiles: 10,
    maxFileSize: 5 * 1024 * 1024, // 5MB

    init() {
        this.previewContainer = document.getElementById('imagePreview');
        this.dropzone = document.getElementById('imageDropzone');
        this.fileInput = document.getElementById('imageUpload');

        this.setupDropzone();
        this.setupFileInput();
        this.setupSortable();
    },

    setupDropzone() {
        this.dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            this.dropzone.classList.add('dragover');
        });

        this.dropzone.addEventListener('dragleave', () => {
            this.dropzone.classList.remove('dragover');
        });

        this.dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            this.dropzone.classList.remove('dragover');
            this.handleFiles(e.dataTransfer.files);
        });

        this.dropzone.addEventListener('click', () => {
            this.fileInput.click();
        });
    },

    setupFileInput() {
        this.fileInput.addEventListener('change', () => {
            this.handleFiles(this.fileInput.files);
        });
    },

    async handleFiles(fileList) {
        const newFiles = Array.from(fileList).filter(file => {
            // Validate file type
            if (!file.type.startsWith('image/')) {
                AlertHandler.error(`${file.name} ไม่ใช่ไฟล์รูปภาพ`);
                return false;
            }

            // Validate file size
            if (file.size > this.maxFileSize) {
                AlertHandler.error(`${file.name} มีขนาดใหญ่เกินไป`);
                return false;
            }

            return true;
        });

        // Check total files limit
        if (this.files.length + newFiles.length > this.maxFiles) {
            AlertHandler.error(`สามารถอัพโหลดได้สูงสุด ${this.maxFiles} ไฟล์`);
            return;
        }

        // Process each file
        for (const file of newFiles) {
            try {
                const preview = await this.createPreview(file);
                this.files.push({
                    file,
                    preview,
                    timestamp: new Date()
                });
            } catch (error) {
                console.error('Error creating preview:', error);
                AlertHandler.error(`ไม่สามารถแสดงตัวอย่างไฟล์ ${file.name} ได้`);
            }
        }

        this.updatePreview();
        this.updateSortControls();
    },

    createPreview(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            
            reader.onload = (e) => resolve(e.target.result);
            reader.onerror = (e) => reject(e);
            reader.readAsDataURL(file);
        });
    },

    updatePreview() {
        this.previewContainer.innerHTML = this.files.map((item, index) => `
            <div class="preview-item" data-index="${index}">
                <img src="${item.preview}" alt="${item.file.name}">
                <div class="preview-controls">
                    <button type="button" onclick="GalleryHandler.viewImage(${index})" title="ดูรูปภาพ">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" onclick="GalleryHandler.removeImage(${index})" title="ลบรูปภาพ">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
    },

    updateSortControls() {
        const controls = document.querySelector('.sort-controls');
        controls.classList.toggle('d-none', this.files.length <= 1);
    },

    viewImage(index) {
        const file = this.files[index];
        ModalHandler.showImagePreview(file.preview);
    },

    removeImage(index) {
        ModalHandler.confirm('คุณต้องการลบรูปภาพนี้หรือไม่?', () => {
            this.files.splice(index, 1);
            this.updatePreview();
            this.updateSortControls();
        });
    },

    setupSortable() {
        if (typeof Sortable !== 'undefined') {
            new Sortable(this.previewContainer, {
                animation: 150,
                ghostClass: 'dragging',
                onEnd: (evt) => {
                    const item = this.files[evt.oldIndex];
                    this.files.splice(evt.oldIndex, 1);
                    this.files.splice(evt.newIndex, 0, item);
                }
            });
        }
    },

    sortByName() {
        this.files.sort((a, b) => a.file.name.localeCompare(b.file.name));
        this.updatePreview();
    },

    sortByDate() {
        this.files.sort((a, b) => b.timestamp - a.timestamp);
        this.updatePreview();
    },

    getFiles() {
        return this.files.map(item => item.file);
    }
};

// Initialize gallery when tab is shown
document.querySelector('button[data-bs-target="#gallery"]').addEventListener('shown.bs.tab', function () {
    GalleryHandler.init();
});