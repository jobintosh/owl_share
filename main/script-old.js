// Initialize TinyMCE
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
    // ลบ language: 'th' ออก
    images_upload_handler: function (blobInfo, success, failure) {
        // ในการใช้งานจริงควรอัพโหลดไปยังเซิร์ฟเวอร์
        // นี่เป็นเพียงตัวอย่างการแปลงเป็น base64
        var reader = new FileReader();
        reader.onload = function () {
            success(reader.result);
        };
        reader.readAsDataURL(blobInfo.blob());
    },
    // เพิ่มการแปล UI เป็นภาษาไทยแบบ Manual
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
            'Cut': 'ตัด',
            'Copy': 'คัดลอก',
            'Paste': 'วาง',
            'Select all': 'เลือกทั้งหมด',
            'New document': 'เอกสารใหม่',
            'Ok': 'ตกลง',
            'Cancel': 'ยกเลิก',
            'Visual aids': 'ตัวช่วยการมองเห็น',
            'Color': 'สี',
            'Custom...': 'กำหนดเอง...',
            'Background color': 'สีพื้นหลัง',
            'Text color': 'สีตัวอักษร'
        }
    },
    // กำหนดภาษาเริ่มต้นเป็นภาษาไทย
    language: 'th'
});

// Global Variables
let canvas, ctx;
let drawing = false;
let currentTool = 'pen';
let currentTags = [];
let sharedItems = [];

// Tags Management
const tagsInput = document.getElementById('tagsInput');
const tagsContainer = document.getElementById('tagsContainer');

tagsInput.addEventListener('keydown', function(e) {
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
    canvas = document.getElementById('whiteboardCanvas');
    ctx = canvas.getContext('2d');
    
    // Set canvas size
    function resizeCanvas() {
        const container = canvas.parentElement;
        canvas.width = container.clientWidth;
        canvas.height = 400;
        ctx.fillStyle = 'white';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
    }
    
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    // Drawing event listeners
    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);

    // Touch events for mobile
    canvas.addEventListener('touchstart', handleTouchStart);
    canvas.addEventListener('touchmove', handleTouchMove);
    canvas.addEventListener('touchend', stopDrawing);

    // Tool buttons
    document.getElementById('penTool').addEventListener('click', () => currentTool = 'pen');
    document.getElementById('eraserTool').addEventListener('click', () => currentTool = 'eraser');
    document.getElementById('clearCanvas').addEventListener('click', clearCanvas);
    
    // Color and size controls
    document.getElementById('colorPicker').addEventListener('change', updateColor);
    document.getElementById('brushSize').addEventListener('change', updateBrushSize);
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

function startDrawing(e) {
    drawing = true;
    draw(e);
}

function draw(e) {
    if (!drawing) return;

    const rect = canvas.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;

    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.lineWidth = document.getElementById('brushSize').value;
    
    if (currentTool === 'eraser') {
        ctx.strokeStyle = 'white';
    } else {
        ctx.strokeStyle = document.getElementById('colorPicker').value;
    }

    ctx.lineTo(x, y);
    ctx.stroke();
    ctx.beginPath();
    ctx.moveTo(x, y);
}

function stopDrawing() {
    drawing = false;
    ctx.beginPath();
}

function clearCanvas() {
    ctx.fillStyle = 'white';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
}

function updateColor(e) {
    currentTool = 'pen';
    document.getElementById('penTool').classList.add('active');
    document.getElementById('eraserTool').classList.remove('active');
}

function updateBrushSize() {
    // Function for future brush size updates if needed
}

// Gallery functionality
function initGallery() {
    const dropzone = document.getElementById('imageDropzone');
    const imageUpload = document.getElementById('imageUpload');
    const imagePreview = document.getElementById('imagePreview');

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
    
    // Get content based on active tab
    const activeTab = document.querySelector('#contentTypeTabs .nav-link.active').getAttribute('data-bs-target');
    let content = '';
    let images = [];

    switch (activeTab) {
        case '#text':
            content = tinymce.get('editor').getContent();
            break;
        case '#whiteboard':
            content = canvas.toDataURL('image/png');
            images.push({
                type: 'whiteboard',
                data: content
            });
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

    const newItem = {
        id: Date.now(),
        title: document.getElementById('titleInput').value,
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
}

function resetForm() {
    shareForm.reset();
    tinymce.get('editor').setContent('');
    clearCanvas();
    document.getElementById('imagePreview').innerHTML = '';
    currentTags = [];
    renderTags();
}

function renderSharedItems() {
    const sharedContent = document.getElementById('sharedContent');
    sharedContent.innerHTML = '';
    
    sharedItems.forEach(item => {
        const itemElement = document.createElement('div');
        itemElement.className = 'shared-item';
        
        let contentHtml = '';
        
        // Generate content based on type
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
                                <img src="${img.data}" alt="Gallery Image">
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
                <button class="btn btn-sm btn-outline-primary like-button">
                    <i class="fas fa-heart"></i> <span class="like-count">${item.likes}</span>
                </button>
                <button class="btn btn-sm btn-outline-primary comment-button ms-2">
                    <i class="fas fa-comment"></i> แสดงความคิดเห็น
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

        // Add event listeners
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
        item.likes++;
        this.querySelector('.like-count').textContent = item.likes;
        this.classList.add('liked');
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
            });
        }
    });

    commentForm.querySelector('button').addEventListener('click', function() {
        if (commentInput.value.trim()) {
            const newComment = {
                author: 'ผู้ใช้งาน',
                content: commentInput.value.trim(),
                timestamp: new Date()
            };
            item.comments.push(newComment);
            
            const commentList = itemElement.querySelector('.comment-list');
            commentList.innerHTML = renderComments(item.comments);
            commentInput.value = '';
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

// Sample Data
function loadSampleData() {
    sharedItems = [
        {
            id: 1,
            title: 'วิธีการใช้ Rich Text Editor',
            content: `
                <p>Rich Text Editor ช่วยให้คุณสามารถจัดการเนื้อหาได้ง่ายขึ้น:</p>
                <ul>
                    <li>จัดรูปแบบตัวอักษร</li>
                    <li>แทรกรูปภาพ</li>
                    <li>สร้างตาราง</li>
                    <li>และอื่นๆ อีกมากมาย</li>
                </ul>
                <figure>
                    <img src="https://via.placeholder.com/600x300" alt="ตัวอย่างรูปภาพ">
                    <figcaption>ตัวอย่างการแทรกรูปภาพพร้อมคำอธิบาย</figcaption>
                </figure>
            `,
            contentType: 'text',
            category: 'technology',
            tags: ['editor', 'การใช้งาน'],
            timestamp: new Date(),
            author: 'แอดมิน',
            likes: 15,
            comments: [
                {
                    author: 'ผู้ใช้ 1',
                    content: 'บทความดีมากครับ เข้าใจง่าย',
                    timestamp: new Date(Date.now() - 3600000)
                }
            ],
            images: []
        },
        {
            id: 2,
            title: 'ตัวอย่างการวาดภาพบน Whiteboard',
            content: '',
            contentType: 'whiteboard',
            category: 'knowledge',
            tags: ['วาดภาพ', 'สอนการใช้งาน'],
            timestamp: new Date(Date.now() - 86400000),
            author: 'ผู้ดูแลระบบ',
            likes: 8,
            comments: [],
            images: [{
                type: 'whiteboard',
                data: 'https://via.placeholder.com/800x600?text=Whiteboard+Example'
            }]
        },
        {
            id: 3,
            title: 'แกลเลอรี่ภาพธรรมชาติ',
            content: '',
            contentType: 'gallery',
            category: 'news',
            tags: ['ธรรมชาติ', 'ภาพถ่าย'],
            timestamp: new Date(Date.now() - 172800000),
            author: 'ช่างภาพ',
            likes: 25,
            comments: [
                {
                    author: 'ผู้ใช้ 2',
                    content: 'ภาพสวยมากครับ',
                    timestamp: new Date(Date.now() - 82800000)
                },
                {
                    author: 'ผู้ใช้ 3',
                    content: 'ช่วยแนะนำเทคนิคการถ่ายภาพหน่อยครับ',
                    timestamp: new Date(Date.now() - 79200000)
                }
            ],
            images: [
                {
                    type: 'gallery',
                    data: 'https://via.placeholder.com/800x600?text=Nature+1'
                },
                {
                    type: 'gallery',
                    data: 'https://via.placeholder.com/800x600?text=Nature+2'
                },
                {
                    type: 'gallery',
                    data: 'https://via.placeholder.com/800x600?text=Nature+3'
                }
            ]
        }
    ];
}

// แก้ไขส่วนของ handleSubmit เพื่อจัดการกับรูปภาพ Whiteboard
async function handleSubmit(e) {
    e.preventDefault();
    
    // Get content based on active tab
    const activeTab = document.querySelector('#contentTypeTabs .nav-link.active').getAttribute('data-bs-target');
    let content = '';
    let images = [];

    switch (activeTab) {
        case '#text':
            content = tinymce.get('editor').getContent();
            break;
        case '#whiteboard':
            try {
                // สร้าง temporary canvas สำหรับลดขนาดรูปภาพ
                const tempCanvas = document.createElement('canvas');
                const tempCtx = tempCanvas.getContext('2d');
                const MAX_WIDTH = 800;
                const MAX_HEIGHT = 600;
                
                let width = canvas.width;
                let height = canvas.height;

                // คำนวณขนาดใหม่โดยรักษาอัตราส่วน
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

                // กำหนดขนาด temporary canvas
                tempCanvas.width = width;
                tempCanvas.height = height;

                // วาดรูปภาพลงใน temporary canvas
                tempCtx.fillStyle = 'white';
                tempCtx.fillRect(0, 0, width, height);
                tempCtx.drawImage(canvas, 0, 0, canvas.width, canvas.height, 0, 0, width, height);

                // แปลงเป็น Blob
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

    const newItem = {
        id: Date.now(),
        title: document.getElementById('titleInput').value,
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
}

// เพิ่มฟังก์ชันทำความสะอาด URL objects
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

// เรียกใช้ cleanup เมื่อปิดหน้าเว็บ
window.addEventListener('beforeunload', cleanup);

// Initialize Share Form
function initShareForm() {
    const shareForm = document.getElementById('shareForm');
    if (shareForm) {
        shareForm.addEventListener('submit', handleSubmit);
    }

    // Tab change handlers
    const contentTabs = document.querySelectorAll('[data-bs-toggle="tab"]');
    contentTabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (e) {
            // Reset current tab content
            resetTabContent(e.target.getAttribute('data-bs-target'));
        });
    });
}

// Reset content when changing tabs
function resetTabContent(tabId) {
    switch (tabId) {
        case '#text':
            tinymce.get('editor').setContent('');
            break;
        case '#whiteboard':
            clearCanvas();
            break;
        case '#gallery':
            document.getElementById('imagePreview').innerHTML = '';
            break;
    }
}

// Search functionality
function initSearch() {
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.className = 'form-control mb-3';
    searchInput.placeholder = 'ค้นหาโพสต์...';
    
    const sharedContent = document.getElementById('sharedContent');
    sharedContent.parentNode.insertBefore(searchInput, sharedContent);

    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const filteredItems = sharedItems.filter(item => 
            item.title.toLowerCase().includes(searchTerm) ||
            item.tags.some(tag => tag.toLowerCase().includes(searchTerm)) ||
            (item.content && typeof item.content === 'string' && 
             item.content.toLowerCase().includes(searchTerm))
        );
        renderSharedItems(filteredItems);
    });
}

// Initialize all components
document.addEventListener('DOMContentLoaded', () => {
    initWhiteboard();
    initGallery();
    initShareForm();
    initSearch();
    loadSampleData();
    renderSharedItems();

    // Enable all tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Enable all popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
});

// Error handling
window.addEventListener('error', function(e) {
    console.error('An error occurred:', e.error);
    // You could show an error message to the user here
});

// Handle browser back/forward
window.addEventListener('popstate', function(e) {
    const hash = window.location.hash;
    if (hash) {
        const tab = document.querySelector(`[data-bs-target="${hash}"]`);
        if (tab) {
            new bootstrap.Tab(tab).show();
        }
    }
});

// Handle visibility changes
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        // Pause any animations or automatic updates
    } else {
        // Resume animations or updates
    }
});

// Handle online/offline status
window.addEventListener('online', function() {
    // Resume full functionality
    document.body.classList.remove('offline-mode');
});

window.addEventListener('offline', function() {
    // Enable offline mode
    document.body.classList.add('offline-mode');
});