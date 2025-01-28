// gallery-handler.js
const GalleryHandler = {
    files: [],
    dropzone: null,
    preview: null,
    maxFiles: 10,
    maxSize: 5 * 1024 * 1024, // 5MB

    init() {
        this.dropzone = document.getElementById('imageDropzone');
        this.preview = document.getElementById('imagePreview');
        this.setupDropzone();
    },

    // ... (โค้ดส่วน Gallery ที่เหลือ)
};
