// Share Form Handler
const ShareForm = {
    submitForm() {
        const formData = new FormData();
        const activeTab = this.getActiveTab();

        // Add basic data
        formData.append('title', document.getElementById('title').value);
        formData.append('category', document.getElementById('category').value);
        formData.append('content_type', activeTab);
        formData.append('tags', JSON.stringify(this.currentTags));

        // Add content based on type
        switch (activeTab) {
            case 'text':
                const content = tinymce.get('editor').getContent({format: 'raw'});
                formData.append('content', content);
                break;

            case 'whiteboard':
                const canvas = document.getElementById('whiteboardCanvas');
                canvas.toBlob((blob) => {
                    formData.append('whiteboard_data', blob, 'whiteboard.png');
                    this.sendFormData(formData);
                }, 'image/png');
                return; // Exit early as we're handling the submit in the callback

            case 'gallery':
                const files = GalleryHandler.getFiles();
                files.forEach((file, index) => {
                    formData.append(`gallery[${index}]`, file);
                });
                break;
        }

        this.sendFormData(formData);
    },

    async sendFormData(formData) {
        try {
            const response = await fetch(`${baseUrl}/share/create`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();
            if (result.success) {
                AlertHandler.success('บันทึกข้อมูลเรียบร้อยแล้ว');
                this.resetForm();
                if (typeof RecentPosts !== 'undefined') {
                    RecentPosts.refresh();
                }
            } else {
                AlertHandler.error(result.message || 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
            }
        } catch (error) {
            console.error('Error submitting form:', error);
            AlertHandler.error('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
        }
    },

    getActiveTab() {
        const activeTab = document.querySelector('#contentTypeTabs .nav-link.active');
        return activeTab.getAttribute('data-bs-target').replace('#', '');
    }
    
    // ... other methods
};