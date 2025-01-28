<?php
/**
 * Text Editor Partial
 */
?>
<div class="editor-container">
    <textarea id="editor" name="content"></textarea>
</div>

<style>
.tox-tinymce {
    border-radius: 0.375rem !important;
}

.editor-container {
    min-height: 400px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    tinymce.init({
        selector: '#editor',
        height: 400,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'styles | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | ' +
                'bullist numlist outdent indent | link image media | removeformat code help',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial; font-size: 14px }',
        language: 'th',
        images_upload_url: baseUrl + '/share/uploadImage',
        automatic_uploads: true,
        images_reuse_filename: true,
        relative_urls: false,
        remove_script_host: false,
        image_caption: true,
        image_dimensions: false,
        paste_data_images: true,
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });
        }
    });
});
</script>