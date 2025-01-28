// Initialize TinyMCE
tinymce.init({
    selector: '#editor',
    height: 400,
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount'
    ],
    toolbar: 'undo redo | formatselect | ' +
        'bold italic backcolor | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent | ' +
        'removeformat | image | help',
    content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial; font-size: 14px }',
    images_upload_handler: function (blobInfo, success, failure) {
        var reader = new FileReader();
        reader.onload = function () {
            success(reader.result);
        };
        reader.readAsDataURL(blobInfo.blob());
    },
    translations: {
        'th': {
            'Bold': 'ตัวหนา',
            'Italic': 'ตัวเอียง',
            'Underline': 'ขีดเส้นใต้',
            'Strikethrough': 'ขีดฆ่า',
            'Align left': 'จัดชิดซ้าย',
            'Align center': 'จัดกึ่งกลาง',
            'Align right': 'จัดชิดขวา',
            'Bullet list': 'รายการแบบจุด',
            'Numbered list': 'รายการแบบตัวเลข',
            'Insert/edit image': 'แทรก/แก้ไขรูปภาพ',
            'Insert/edit link': 'แทรก/แก้ไขลิงก์',
            'Insert table': 'แทรกตาราง',
            'Font Family': 'แบบอักษร',
            'Font Sizes': 'ขนาดตัวอักษร',
            'Formats': 'รูปแบบ',
            'Blockquote': 'ข้อความอ้างอิง',
            'Undo': 'เลิกทำ',
            'Redo': 'ทำซ้ำ',
            'Ok': 'ตกลง',
            'Cancel': 'ยกเลิก'
        }
    },
    language: 'th'
});

// Global Variables
let canvas, ctx;
let isDrawing = false;
let lastX, lastY;
let currentTool = 'pen';
let currentColor = '#000000';
let currentSize = 5;
let currentTags = [];
let sharedItems = [];

// Tags Management
const tagsInput = document.getElementById('tagsInput');
const tagsContainer = document.getElementById('tagsContainer');

tagsInput?.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const tag = this.value.trim();
        if (tag && !currentTags.includes(tag)) {
            currentTags.push(tag);
            renderTags();
        }
        this.value = '';
    }
});

function renderTags() {
    if (!tagsContainer) return;
    tagsContainer.innerHTML = currentTags.map(tag => `
        <span class="tag-badge">
            ${escapeHtml(tag)}
            <span class="remove-tag" onclick="removeTag('${escapeHtml(tag)}')">×</span>
        </span>
    `).join('');
}

function removeTag(tag) {
    currentTags = currentTags.filter(t => t !== tag);
    renderTags();
}

// Whiteboard functionality
function initWhiteboard() {
    console.log('Initializing whiteboard...');
    canvas = document.getElementById('whiteboardCanvas');
    if (!canvas) {
        console.error('Canvas element not found');
        return;
    }

    ctx = canvas.getContext('2d');
    setupCanvas();
    setupTools();
    addEventListeners();
}

function setupCanvas() {
    console.log('Setting up canvas...');
    const container = canvas.parentElement;
    const styles = window.getComputedStyle(container);
    const width = parseInt(styles.width, 10) - 20;

    canvas.width = width;
    canvas.height = 400;

    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
}

function setupTools() {
    console.log('Setting up tools...');
    const penTool = document.getElementById('penTool');
    const eraserTool = document.getElementById('eraserTool');
    const clearButton = document.getElementById('clearCanvas');
    const colorPicker = document.getElementById('colorPicker');
    const brushSize = document.getElementById('brushSize');

    if (penTool) {
        penTool.addEventListener('click', () => {
            currentTool = 'pen';
            penTool.classList.add('active');
            eraserTool?.classList.remove('active');
        });
    }

    if (eraserTool) {
        eraserTool.addEventListener('click', () => {
            currentTool = 'eraser';
            eraserTool.classList.add('active');
            penTool?.classList.remove('active');
        });
    }

    if (clearButton) {
        clearButton.addEventListener('click', () => {
            clearCanvas();
        });
    }

    if (colorPicker) {
        colorPicker.addEventListener('change', (e) => {
            currentColor = e.target.value;
            currentTool = 'pen';
            penTool?.classList.add('active');
            eraserTool?.classList.remove('active');
        });
    }

    if (brushSize) {
        brushSize.addEventListener('change', (e) => {
            currentSize = parseInt(e.target.value, 10);
        });
    }
}

function addEventListeners() {
    console.log('Adding event listeners...');
    // Mouse Events
    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);

    // Touch Events
    canvas.addEventListener('touchstart', handleTouchStart, { passive: false });
    canvas.addEventListener('touchmove', handleTouchMove, { passive: false });
    canvas.addEventListener('touchend', stopDrawing);

    // Window Resize
    window.addEventListener('resize', debounce(() => {
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        setupCanvas();
        ctx.putImageData(imageData, 0, 0);
    }, 250));
}

function startDrawing(e) {
    isDrawing = true;
    const pos = getMousePos(e);
    lastX = pos.x;
    lastY = pos.y;
    // Draw a single point for dot drawing
    drawPoint(lastX, lastY);
}

function draw(e) {
    if (!isDrawing) return;
    e.preventDefault();

    const pos = getMousePos(e);
    const currentX = pos.x;
    const currentY = pos.y;

    ctx.beginPath();
    ctx.moveTo(lastX, lastY);
    ctx.lineTo(currentX, currentY);
    ctx.strokeStyle = currentTool === 'eraser' ? '#ffffff' : currentColor;
    ctx.lineWidth = currentSize;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.stroke();

    lastX = currentX;
    lastY = currentY;
}

function drawPoint(x, y) {
    ctx.beginPath();
    ctx.arc(x, y, currentSize / 2, 0, Math.PI * 2);
    ctx.fillStyle = currentTool === 'eraser' ? '#ffffff' : currentColor;
    ctx.fill();
}

function stopDrawing() {
    isDrawing = false;
}

function handleTouchStart(e) {
    e.preventDefault();
    const touch = e.touches[0];
    const mouseEvent = new MouseEvent('mousedown', {
        clientX: touch.clientX,
        clientY: touch.clientY
    });
    canvas.dispatchEvent(mouseEvent);
}

function handleTouchMove(e) {
    e.preventDefault();
    const touch = e.touches[0];
    const mouseEvent = new MouseEvent('mousemove', {
        clientX: touch.clientX,
        clientY: touch.clientY
    });
    canvas.dispatchEvent(mouseEvent);
}

function getMousePos(e) {
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;

    if (e.touches && e.touches[0]) {
        return {
            x: (e.touches[0].clientX - rect.left) * scaleX,
            y: (e.touches[0].clientY - rect.top) * scaleY
        };
    }

    return {
        x: (e.clientX - rect.left) * scaleX,
        y: (e.clientY - rect.top) * scaleY
    };
}

function clearCanvas() {
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Gallery functionality
function initGallery() {
    const dropzone = document.getElementById('imageDropzone');
    const imageUpload = document.getElementById('imageUpload');
    
    if (!dropzone || !imageUpload) return;

    dropzone.addEventListener('click', () => imageUpload.click());
    
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('dragover');
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('dragover');
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('dragover');
        handleFiles(e.dataTransfer.files);
    });

    imageUpload.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });
}

function handleFiles(files) {
    const imagePreview = document.getElementById('imagePreview');
    if (!imagePreview) return;
    
    Array.from(files).forEach(file => {
        if (!file.type.startsWith('image/')) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';
            previewItem.innerHTML = `
                <img src="${e.target.result}" alt="Preview">
                <button type="button" class="remove-image">
                    <i class="fas fa-times"></i>
                </button>
            `;

            previewItem.querySelector('.remove-image').addEventListener('click', () => {
                previewItem.remove();
            });

            imagePreview.appendChild(previewItem);
        };
        reader.readAsDataURL(file);
    });
}

// Form Submission and Content Rendering
async function handleSubmit(e) {
    e.preventDefault();
    
    const activeTab = document.querySelector('#contentTypeTabs .nav-link.active').getAttribute('data-bs-target');
    let content = '';
    let images = [];

    switch (activeTab) {
        case '#text':
            content = tinymce.get('editor').getContent();
            break;
        case '#whiteboard':
            try {
                const tempCanvas = document.createElement('canvas');
                const tempCtx = tempCanvas.getContext('2d');
                const MAX_WIDTH = 800;
                const MAX_HEIGHT = 600;
                
                let width = canvas.width;
                let height = canvas.height;

                if (width > height) {
                    if (width > MAX_WIDTH) {
                        height *= MAX_WIDTH / width;
                        width = MAX_WIDTH;
                    }
                } else {
                    if (height > MAX_HEIGHT) {
                        width *= MAX_HEIGHT / height;
                        height = MAX_HEIGHT;
                    }
                }

                tempCanvas.width = width;
                tempCanvas.height = height;

                tempCtx.fillStyle = 'white';
                tempCtx.fillRect(0, 0, width, height);
                tempCtx.drawImage(canvas, 0, 0, canvas.width, canvas.height, 0, 0, width, height);

                const blob = await new Promise(resolve => tempCanvas.toBlob(resolve, 'image/jpeg', 0.8));
                const imageUrl = URL.createObjectURL(blob);
                
                images.push({
                    type: 'whiteboard',
                    data: imageUrl
                });
                content = imageUrl;
            } catch (error) {
                console.error('Error processing whiteboard image:', error);
                alert('เกิดข้อผิดพลาดในการประมวลผลรูปภาพ');
                return;
            }
            break;
        case '#gallery':
            const previewItems = document.querySelectorAll('.preview-item img');
            previewItems.forEach(img => {
                images.push({
                    type: 'gallery',
                    data: img.src
                });
            });
            break;
    }

    if (!content && images.length === 0) {
        alert('กรุณาใส่เนื้อหาหรือรูปภาพ');
        return;
    }

    const titleInput = document.getElementById('titleInput');
    if (!titleInput.value.trim()) {
        alert('กรุณาใส่หัวข้อ');
        return;
    }

    const newItem = {
        id: Date.now(),
        title: titleInput.value,
        content: content,
        images: images,
        category: document.getElementById('categorySelect').value,
        tags: [...currentTags],
        timestamp: new Date(),
        author: 'ผู้ใช้งาน',
        contentType: activeTab.replace('#', ''),
        likes: 0,
        comments: []
    };

    sharedItems.unshift(newItem);
    renderSharedItems();
    resetForm();

    // Switch to the first tab
    const firstTab = document.querySelector('#contentTypeTabs .nav-link');
    if (firstTab) {
        new bootstrap.Tab(firstTab).show();
    }
}

function resetForm() {
    const form = document.getElementById('shareForm');
    if (!form) return;

    form.reset();
    if (tinymce.get('editor')) {
        tinymce.get('editor').setContent('');
    }
    if (canvas) {
        clearCanvas();
    }
    const imagePreview = document.getElementById('imagePreview');
    if (imagePreview) {
        imagePreview.innerHTML = '';
    }
    currentTags = [];
    renderTags();
}

function renderSharedItems() {
    const sharedContent = document.getElementById('sharedContent');
    if (!sharedContent) return;
    
    sharedContent.innerHTML = '';
    
    sharedItems.forEach(item => {
        const itemElement = document.createElement('div');
        itemElement.className = 'shared-item';
        
        let contentHtml = '';
        
        switch (item.contentType) {
            case 'text':
                contentHtml = item.content;
                break;
            case 'whiteboard':
                contentHtml = `<img src="${item.images[0].data}" alt="Whiteboard" class="whiteboard-image">`;
                break;
            case 'gallery':
                contentHtml = `
                    <div class="gallery-grid">
                        ${item.images.map(img => `
                            <div class="gallery-item">
                                <img src="${img.data}" alt="Gallery Image" loading="lazy">
                            </div>
                        `).join('')}
                    </div>
                `;
                break;
        }
        
        itemElement.innerHTML = `
            <div class="title">${escapeHtml(item.title)}</div>
            <div class="content">${contentHtml}</div>
            <div class="metadata">
                <span class="category-badge">${getCategoryText(item.category)}</span>
                ${item.tags.map(tag => `<span class="tag-badge">${escapeHtml(tag)}</span>`).join('')}
                <span class="ms-3">
                    <i class="fas fa-user"></i> ${escapeHtml(item.author)}
                </span>
                <span class="ms-3">
                    <i class="fas fa-clock"></i> ${formatDate(item.timestamp)}
                </span>
            </div>
            <div class="actions mt-2">
                <button class="btn btn-sm btn-outline-primary like-button ${item.liked ? 'liked' : ''}">
                    <i class="fas fa-heart"></i> <span class="like-count">${item.likes}</span>
                </button>
                <button class="btn btn-sm btn-outline-primary comment-button ms-2">
                    <i class="fas fa-comment"></i> แสดงความคิดเห็น
                    <span class="comment-count">(${item.comments.length})</span>
                </button>
                <button class="btn btn-sm btn-outline-primary share-button ms-2">
                    <i class="fas fa-share"></i> แชร์
                </button>
            </div>
            <div class="comments-section mt-3" style="display: none;">
                <div class="comment-list">
                    ${renderComments(item.comments)}
                </div>
                <div class="comment-form mt-2">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="เขียนความคิดเห็น...">
                        <button class="btn btn-primary" type="button">ส่ง</button>
                    </div>
                </div>
            </div>
        `;

        attachItemEventListeners(itemElement, item);
        sharedContent.appendChild(itemElement);
    });
}

function attachItemEventListeners(itemElement, item) {
    const likeButton = itemElement.querySelector('.like-button');
    const commentButton = itemElement.querySelector('.comment-button');
    const shareButton = itemElement.querySelector('.share-button');
    const commentsSection = itemElement.querySelector('.comments-section');
    const commentForm = itemElement.querySelector('.comment-form');
    const commentInput = commentForm.querySelector('input');

    likeButton.addEventListener('click', function() {
        if (!item.liked) {
            item.likes++;
            item.liked = true;
            this.classList.add('liked');
        } else {
            item.likes--;
            item.liked = false;
            this.classList.remove('liked');
        }
        this.querySelector('.like-count').textContent = item.likes;
    });

    commentButton.addEventListener('click', function() {
        commentsSection.style.display = 
            commentsSection.style.display === 'none' ? 'block' : 'none';
    });

    shareButton.addEventListener('click', function() {
        if (navigator.share) {
            navigator.share({
                title: item.title,
                text: `ดู "${item.title}" บน ShareHub`,
                url: window.location.href
            }).catch(console.error);
        } else {
            // Fallback for browsers that don't support Web Share API
            const dummy = document.createElement('textarea');
            document.body.appendChild(dummy);
            dummy.value = window.location.href;
            dummy.select();
            document.execCommand('copy');
            document.body.removeChild(dummy);
            alert('คัดลอก URL แล้ว');
        }
    });

    commentForm.querySelector('button').addEventListener('click', function() {
        const commentText = commentInput.value.trim();
        if (commentText) {
            const newComment = {
                author: 'ผู้ใช้งาน',
                content: commentText,
                timestamp: new Date()
            };
            item.comments.push(newComment);
            
            const commentList = itemElement.querySelector('.comment-list');
            commentList.innerHTML = renderComments(item.comments);
            commentInput.value = '';

            // Update comment count
            const commentCount = itemElement.querySelector('.comment-count');
            commentCount.textContent = `(${item.comments.length})`;
        }
    });

    // Add enter key support for comments
    commentInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            commentForm.querySelector('button').click();
        }
    });
}

function renderComments(comments) {
    return comments.map(comment => `
        <div class="comment-item p-2 border-bottom">
            <div class="d-flex justify-content-between">
                <strong>${escapeHtml(comment.author)}</strong>
                <small class="text-muted">${formatDate(comment.timestamp)}</small>
            </div>
            <div>${escapeHtml(comment.content)}</div>
        </div>
    `).join('');
}

// Helper Functions
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function getCategoryText(category) {
    const categories = {
        knowledge: 'ความรู้',
        news: 'ข่าวสาร',
        technology: 'เทคโนโลยี'
    };
    return categories[category] || category;
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Cleanup function
function cleanup() {
    sharedItems.forEach(item => {
        if (item.images) {
            item.images.forEach(img => {
                if (img.data && img.data.startsWith('blob:')) {
                    URL.revokeObjectURL(img.data);
                }
            });
        }
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    initWhiteboard();
    initGallery();
    loadSampleData();
    renderSharedItems();

    // Add form submit handler
    const shareForm = document.getElementById('shareForm');
    if (shareForm) {
        shareForm.addEventListener('submit', handleSubmit);
    }

    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Add tab change handler
    const tabs = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', (e) => {
            if (e.target.getAttribute('data-bs-target') === '#whiteboard') {
                // Reinitialize whiteboard when switching to whiteboard tab
                initWhiteboard();
            }
        });
    });
});

// Cleanup before page unload
window.addEventListener('beforeunload', cleanup);

// Add necessary styles
const style = document.createElement('style');
style.textContent = `
    .whiteboard-container {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 20px;
    }

    .whiteboard-tools {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }

    #whiteboardCanvas {
        width: 100%;
        height: 400px;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        cursor: crosshair;
        touch-action: none;
    }

    .btn.active {
        background-color: #0d6efd !important;
        color: white !important;
    }

    #colorPicker {
        width: 40px;
        padding: 0;
        height: 31px;
    }

    #brushSize {
        width: 80px;
    }
`;
document.head.appendChild(style);