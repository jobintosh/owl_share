<?php
/**
 * Share Scripts Partial
 */
?>
<!-- Core Share Scripts -->
<script>
// Share Form Handler
const ShareForm = {
    init() {
        this.form = document.getElementById('shareForm');
        this.submitBtn = document.getElementById('submitBtn');
        this.currentTags = [];
        
        this.initFormSubmit();
        this.initTagsInput();
        this.initTabChange();

       // alert("I am an alert box!");
    },

    initFormSubmit() {
        if (!this.form) return;

        this.form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
           // if (!this.validateForm()) return;

            try {
                await this.submitForm();
            } catch (error) {
                console.error('Error submitting form:', error);
                AlertHandler.error('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง d5');
            }
        });
    },

    validateForm() {
        // ตรวจสอบหัวข้อ
        const title = document.getElementById('title').value.trim();
        if (title.length < 5) {
            AlertHandler.error('หัวข้อต้องมีความยาวอย่างน้อย 5 ตัวอักษร');
            return false;
        }

        // ตรวจสอบหมวดหมู่
        const category = document.getElementById('category').value;
        if (!category) {
            AlertHandler.error('กรุณาเลือกหมวดหมู่');
            return false;
        }

        // ตรวจสอบเนื้อหาตามประเภท
        const activeTab = document.querySelector('#contentTypeTabs .nav-link.active');
        const contentType = activeTab.getAttribute('data-bs-target').replace('#', '');
        
        switch (contentType) {
            case 'text':
                if (!tinymce.get('editor').getContent()) {
                    AlertHandler.error('กรุณาใส่เนื้อหา');
                    return false;
                }
                break;

            case 'whiteboard':
                const canvas = document.getElementById('whiteboardCanvas');
                const context = canvas.getContext('2d');
                const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                const isEmpty = !imageData.data.some(channel => channel !== 255);
                
                if (isEmpty) {
                    AlertHandler.error('กรุณาวาดภาพ');
                    return false;
                }
                break;

            case 'gallery':
                if (!GalleryHandler.getFiles().length) {
                    AlertHandler.error('กรุณาเลือกรูปภาพ');
                    return false;
                }
                break;
        }

        return true;
    },

    // async submitForm() {
    //     this.setLoading(true);
    //     const formData = new FormData();

        
        
    //     // เพิ่มข้อมูลพื้นฐาน
    //     formData.append('title', document.getElementById('title').value);
    //     formData.append('category', document.getElementById('category').value);
    //     formData.append('tags', JSON.stringify(this.currentTags));


    //     console.log(document.getElementById('title').value);
    //     console.log(document.getElementById('category').value);
    //     console.log(JSON.stringify(this.currentTags));
    //     console.log(tinymce.get('editor').getContent());

    //     // เพิ่มประเภทเนื้อหา
    //     const activeTab = document.querySelector('#contentTypeTabs .nav-link.active');
    //     const contentType = activeTab.getAttribute('data-bs-target').replace('#', '');
    //     formData.append('content_type', contentType);

    //     // เพิ่มเนื้อหาตามประเภท
    //     switch (contentType) {
    //         case 'text':
    //             formData.append('content', tinymce.get('editor').getContent());
    //             break;

    //         case 'whiteboard':
    //             const canvas = document.getElementById('whiteboardCanvas');
    //             const imageData = canvas.toDataURL('image/png');
    //             formData.append('whiteboard_data', imageData);
    //             break;

    //         case 'gallery':
    //             const files = GalleryHandler.getFiles();
    //             files.forEach(file => {
    //                 formData.append('gallery[]', file);
    //             });
    //             break;
    //     }

    //     console.log(formData);

    //     try {
    //         const response = await fetch(`${baseUrl}/share/create`, {
    //             method: 'POST',
    //             body: formData
    //         });

    //         const result = await response.json();

    //         if (result.success) {
    //             AlertHandler.success('บันทึกข้อมูลเรียบร้อยแล้ว');
    //             this.resetForm();
    //             RecentPosts.refresh();
                
    //             // เลื่อนไปด้านบนแบบ smooth
    //             window.scrollTo({
    //                 top: 0,
    //                 behavior: 'smooth'
    //             });
    //         } else {
    //             AlertHandler.error(result.message || 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้งdddddd');
    //         }
    //     } finally {
    //         this.setLoading(false);
    //     }
    // },

    // setLoading(loading) {
    //     this.submitBtn.disabled = loading;
    //     this.submitBtn.innerHTML = loading ?
    //         '<span class="spinner-border spinner-border-sm me-2"></span>กำลังบันทึก...' :
    //         '<i class="fas fa-share"></i> แบ่งปัน';
    // },



    async submitForm() {
   // this.setLoading(true);
    const formData = new FormData();
    
    // เพิ่ม CSRF Token
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    // หรือ
    // const csrfName = document.querySelector('meta[name="X-CSRF-TOKEN"]').getAttribute('content');
      const csrfHash = document.querySelector('meta[name="X-CSRF-HASH"]').getAttribute('content');
    // formData.append(csrfName, csrfHash);
    
    // เพิ่มข้อมูลพื้นฐาน
    formData.append('title', document.getElementById('title').value);
    formData.append('category', document.getElementById('category').value);
    formData.append('tags', JSON.stringify(this.currentTags));
    
    // เพิ่มประเภทเนื้อหา
    const activeTab = document.querySelector('#contentTypeTabs .nav-link.active');
    const contentType = activeTab.getAttribute('data-bs-target').replace('#', '');
    formData.append('content_type', contentType);
    
    // เพิ่มเนื้อหาตามประเภท
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
            const files = GalleryHandler.getFiles();
            files.forEach(file => {
                formData.append('gallery[]', file);
            });
            break;
    }

    try {
        const response = await fetch(`${baseUrl}/share/create`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                // กรณีต้องการส่ง CSRF Token ผ่าน Headers
                'X-CSRF-TOKEN': csrfHash
            },
            body: formData,
            credentials: 'same-origin' // สำคัญสำหรับ CSRF
        });

        const result = await response.json();

        if (result.success) {
            AlertHandler.success('บันทึกข้อมูลเรียบร้อยแล้ว');
            this.resetForm();
           // RecentPosts.refresh();
            
            // เลื่อนไปด้านบนแบบ smooth

            console.log(result.postId);
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        } else {
            AlertHandler.error(result.message || 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
        }
    } catch (error) {
        console.error('Error:', error);
        AlertHandler.error('เกิดข้อผิดพลาดในการเชื่อมต่อ');
    } finally {
       // this.setLoading(false);
    }
},




    resetForm() {
        // รีเซ็ตฟอร์ม
        this.form.reset();
        
        // รีเซ็ต TinyMCE
        if (tinymce.get('editor')) {
            tinymce.get('editor').setContent('');
        }

        // รีเซ็ต Whiteboard
        const canvas = document.getElementById('whiteboardCanvas');
        if (canvas) {
            const context = canvas.getContext('2d');
            context.fillStyle = 'white';
            context.fillRect(0, 0, canvas.width, canvas.height);
        }

        // รีเซ็ต Gallery
        if (GalleryHandler.resetGallery) {
            GalleryHandler.resetGallery();
        }

        // รีเซ็ต Tags
        this.currentTags = [];
        this.renderTags();

        // กลับไปแท็บแรก
        const firstTab = document.querySelector('#contentTypeTabs .nav-link');
        if (firstTab) {
            new bootstrap.Tab(firstTab).show();
        }
    },

    initTagsInput() {
        const input = document.getElementById('tagsInput');
        const addBtn = document.getElementById('addTagBtn');
        
        if (!input || !addBtn) return;

        const addTag = () => {
            const tag = input.value.trim();
            if (tag && !this.currentTags.includes(tag)) {
                this.currentTags.push(tag);
                this.renderTags();
                input.value = '';
            }
        };

        // เพิ่ม Tag เมื่อกด Enter
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                addTag();
            }
        });

        // เพิ่ม Tag เมื่อคลิกปุ่ม
        addBtn.addEventListener('click', addTag);
    },

    renderTags() {
        const container = document.getElementById('tagsContainer');
        if (!container) return;

        container.innerHTML = this.currentTags.map(tag => `
            <span class="tag-badge">
                ${this.escapeHtml(tag)}
                <button type="button" class="btn-close btn-close-white ms-2" 
                        onclick="ShareForm.removeTag('${this.escapeHtml(tag)}')"></button>
            </span>
        `).join('');
    },

    removeTag(tag) {
        this.currentTags = this.currentTags.filter(t => t !== tag);
        this.renderTags();
    },

    addTag(tag) {
        if (!this.currentTags.includes(tag)) {
            this.currentTags.push(tag);
            this.renderTags();
        }
    },

    initTabChange() {
        // จัดการเมื่อมีการเปลี่ยนแท็บ
        document.querySelectorAll('#contentTypeTabs .nav-link').forEach(tab => {
            tab.addEventListener('shown.bs.tab', (e) => {
                const target = e.target.getAttribute('data-bs-target');
                if (target === '#whiteboard') {
                    WhiteboardHandler.initWhiteboard();
                }
            });
        });
    },

    escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    ShareForm.init();
});
</script>

<!-- Include other handlers -->
<script src="<?= base_url('js/handlers/whiteboard-handler.js') ?>"></script>
<script src="<?= base_url('js/handlers/gallery-handler.js') ?>"></script>
<script src="<?= base_url('js/handlers/post-card-handler.js') ?>"></script>