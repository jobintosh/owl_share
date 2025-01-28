/**
 * Share Helpers
 */

const ShareHelpers = {
    // สำหรับเก็บข้อมูล TinyMCE Editor
    editor: null,

    /**
     * เริ่มต้นการทำงาน
     */
    init() {
        this.initTinyMCE();
        this.initWhiteboard();
        this.initGallery();
        this.initTags();
        this.initFormSubmit();
    },

    /**
     * เริ่มต้น TinyMCE Editor
     */
    initTinyMCE() {
        tinymce.init({
            selector: '#editor',
            height: 400,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 
                'charmap', 'preview', 'searchreplace', 'visualblocks', 
                'code', 'fullscreen', 'insertdatetime', 'media', 
                'table', 'wordcount'
            ],
            toolbar: 'undo redo | formatselect | ' +
                    'bold italic backcolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat | image | help',
            language: 'th',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial; font-size: 14px }',
            setup: (editor) => {
                this.editor = editor;
                editor.on('change', () => {
                    editor.save();
                });
            }
        });
    },

    /**
     * เริ่มต้น Whiteboard
     */
    initWhiteboard() {
        const canvas = document.getElementById('whiteboardCanvas');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        let isDrawing = false;
        let lastX = 0;
        let lastY = 0;

        // ปรับขนาด Canvas
        const resizeCanvas = () => {
            const container = canvas.parentElement;
            canvas.width = container.offsetWidth - 20;
            canvas.height = 400;
            
            // เติมพื้นหลังสีขาว
            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
        };

        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);

        // Event Listeners
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);

        // Touch Events
        canvas.addEventListener('touchstart', handleTouchStart);
        canvas.addEventListener('touchmove', handleTouchMove);
        canvas.addEventListener('touchend', stopDrawing);

        // Tool Buttons
        document.getElementById('penTool')?.addEventListener('click', () => {
            ctx.strokeStyle = document.getElementById('colorPicker').value;
            updateToolButtons('penTool');
        });

        document.getElementById('eraserTool')?.addEventListener('click', () => {
            ctx.strokeStyle = '#ffffff';
            updateToolButtons('eraserTool');
        });

        document.getElementById('colorPicker')?.addEventListener('change', (e) => {
            ctx.strokeStyle = e.target.value;
            updateToolButtons('penTool');
        });

        document.getElementById('brushSize')?.addEventListener('change', (e) => {
            ctx.lineWidth = e.target.value;
        });

        document.getElementById('clearCanvas')?.addEventListener('click', () => {
            if (confirm('คุณต้องการล้างกระดานใช่หรือไม่?')) {
                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
            }
        });

        // Drawing Functions
        function startDrawing(e) {
            isDrawing = true;
            const pos = getMousePos(e);
            [lastX, lastY] = [pos.x, pos.y];
        }

        function draw(e) {
            if (!isDrawing) return;
            e.preventDefault();

            const pos = getMousePos(e);
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();

            [lastX, lastY] = [pos.x, pos.y];
        }

        function stopDrawing() {
            isDrawing = false;
        }

        function getMousePos(e) {
            const rect = canvas.getBoundingClientRect();
            return {
                x: (e.clientX || e.touches[0].clientX) - rect.left,
                y: (e.clientY || e.touches[0].clientY) - rect.top
            };
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

        function updateToolButtons(activeId) {
            document.querySelectorAll('.whiteboard-tools .btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.getElementById(activeId)?.classList.add('active');
        }

        // Initial Setup
        ctx.strokeStyle = '#000000';
        ctx.lineWidth = 5;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
    },

    /**
     * เริ่มต้น Gallery
     */
    initGallery() {
        const dropzone = document.getElementById('imageDropzone');
        const imageUpload = document.getElementById('imageUpload');
        const preview = document.getElementById('imagePreview');
        
        if (!dropzone || !imageUpload || !preview) return;

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

        dropzone.addEventListener('click', () => {
            imageUpload.click();
        });

        imageUpload.addEventListener('change', () => {
            handleFiles(imageUpload.files);
        });

        function handleFiles(files) {
            Array.from(files).forEach(file => {
                if (!file.type.startsWith('image/')) {
                    alert('กรุณาอัพโหลดไฟล์รูปภาพเท่านั้น');
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.className = 'preview-item';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <div class="preview-controls">
                            <button type="button" class="remove-image">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;

                    div.querySelector('.remove-image').addEventListener('click', () => {
                        div.remove();
                    });

                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }
    },

    /**
     * เริ่มต้นระบบ Tags
     */
    initTags() {
        const tagsInput = document.getElementById('tagsInput');
        const addTagBtn = document.getElementById('addTagBtn');
        const tagsContainer = document.getElementById('tagsContainer');
        
        if (!tagsInput || !addTagBtn || !tagsContainer) return;

        let tags = [];

        const addTag = (tag) => {
            tag = tag.trim();
            if (tag && !tags.includes(tag)) {
                tags.push(tag);
                renderTags();
                tagsInput.value = '';
            }
        };

        tagsInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                addTag(tagsInput.value);
            }
        });

        addTagBtn.addEventListener('click', () => {
            addTag(tagsInput.value);
        });

        function renderTags() {
            tagsContainer.innerHTML = tags.map(tag => `
                <span class="tag-badge">
                    ${escapeHtml(tag)}
                    <button type="button" class="btn-close btn-close-white ms-2" 
                            onclick="ShareHelpers.removeTag('${escapeHtml(tag)}')">
                    </button>
                </span>
            `).join('');
        }

        this.addTag = addTag;
        this.getTags = () => tags;
        this.removeTag = (tag) => {
            tags = tags.filter(t => t !== tag);
            renderTags();
        };
    },

    /**
     * เริ่มต้น Form Submit
     */
    initFormSubmit() {
        const form = document.getElementById('shareForm');
        if (!form) return;

        // form.addEventListener('submit', async (e) => {
        //     e.preventDefault();
            
        //     // if (!this.validateForm()) {
        //     //     return;
        //     // }

        //     try {
        //         await this.submitForm();
        //     } catch (error) {
        //         console.error('Error submitting form:', error);
        //         alert('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง fdsfd');
        //     }
        // });
    },

    /**
     * ตรวจสอบข้อมูลฟอร์ม
     */
    validateForm() {
        const title = document.getElementById('title').value.trim();
        if (title.length < 5) {
            alert('หัวข้อต้องมีความยาวอย่างน้อย 5 ตัวอักษร');
            return false;
        }

        const category = document.getElementById('category').value;
        if (!category) {
            alert('กรุณาเลือกหมวดหมู่');
            return false;
        }

        return true;
    },

    /**
     * ส่งข้อมูลฟอร์ม
     */
    async submitForm() {
        const formData = new FormData();
        const activeTab = document.querySelector('#contentTypeTabs .nav-link.active');
        const contentType = activeTab.getAttribute('data-bs-target').replace('#', '');

        formData.append('title', document.getElementById('title').value);
        formData.append('category', document.getElementById('category').value);
        formData.append('content_type', contentType);
        formData.append('tags', JSON.stringify(this.getTags()));

        switch (contentType) {
            case 'text':
                formData.append('content', this.editor.getContent());
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
                    formData.append(`gallery[${index}]`, img.src);
                });
                break;
        }

        const response = await fetch('${baseUrl}/share/create', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        
        if (result.success) {
            alert('บันทึกข้อมูลเรียบร้อยแล้ว');
            window.location.reload();
        } else {
            alert(result.message || 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
        }
    }
};

// Utility Functions
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    ShareHelpers.init();
});