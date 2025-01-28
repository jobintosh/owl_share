<div class="modal fade" id="shareModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">แชร์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="share-options">
                    <!-- Social Share Buttons -->
                    <div class="d-flex justify-content-center gap-3 mb-4">
                        <button class="btn btn-outline-primary" onclick="shareToFacebook()">
                            <i class="fab fa-facebook"></i> Facebook
                        </button>
                        <button class="btn btn-outline-info" onclick="shareToTwitter()">
                            <i class="fab fa-twitter"></i> Twitter
                        </button>
                        <button class="btn btn-outline-success" onclick="shareToLine()">
                            <i class="fab fa-line"></i> Line
                        </button>
                    </div>

                    <!-- Copy Link -->
                    <div class="input-group">
                        <input type="text" id="shareUrl" class="form-control" readonly>
                        <button class="btn btn-primary" onclick="copyShareUrl()">
                            <i class="fas fa-copy"></i> คัดลอก
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ดูรูปภาพ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <img src="" class="img-fluid w-100" id="previewImage">
            </div>
        </div>
    </div>
</div>

<!-- Confirm Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ยืนยัน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage">คุณต้องการดำเนินการนี้หรือไม่?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" id="confirmButton">ยืนยัน</button>
            </div>
        </div>
    </div>
</div>

<script>
// Modal Handler
const ModalHandler = {
    initShareModal(url, title = '') {
        const modal = document.getElementById('shareModal');
        const urlInput = modal.querySelector('#shareUrl');
        urlInput.value = url;

        // Update share buttons
        window.shareUrl = url;
        window.shareTitle = title;
    },

    showImagePreview(imageUrl) {
        const modal = document.getElementById('imagePreviewModal');
        const img = modal.querySelector('#previewImage');
        img.src = imageUrl;
        new bootstrap.Modal(modal).show();
    },

    confirm(message, callback) {
        const modal = document.getElementById('confirmModal');
        const messageEl = modal.querySelector('#confirmMessage');
        const confirmBtn = modal.querySelector('#confirmButton');
        
        messageEl.textContent = message;
        
        // Remove existing listener
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
        
        // Add new listener
        newConfirmBtn.addEventListener('click', () => {
            bootstrap.Modal.getInstance(modal).hide();
            callback();
        });

        new bootstrap.Modal(modal).show();
    }
};

// Share Functions
function shareToFacebook() {
    const url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.shareUrl)}`;
    window.open(url, '_blank');
}

function shareToTwitter() {
    const url = `https://twitter.com/intent/tweet?url=${encodeURIComponent(window.shareUrl)}&text=${encodeURIComponent(window.shareTitle)}`;
    window.open(url, '_blank');
}

function shareToLine() {
    const url = `https://social-plugins.line.me/lineit/share?url=${encodeURIComponent(window.shareUrl)}`;
    window.open(url, '_blank');
}

function copyShareUrl() {
    const urlInput = document.getElementById('shareUrl');
    urlInput.select();
    document.execCommand('copy');
    
    // Show tooltip
    const btn = urlInput.nextElementSibling;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check"></i> คัดลอกแล้ว';
    setTimeout(() => {
        btn.innerHTML = originalText;
    }, 2000);
}

// Initialize tooltips for modals
document.addEventListener('shown.bs.modal', function () {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
});
</script>