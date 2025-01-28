<div class="loading-overlay d-none" id="loadingOverlay">
    <div class="loading-spinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">กำลังโหลด...</span>
        </div>
        <div class="mt-2">กำลังดำเนินการ...</div>
    </div>
</div>

<style>
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(5px);
}

.loading-spinner {
    text-align: center;
    padding: 2rem;
    background: white;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}

.loading-spinner .spinner-border {
    width: 3rem;
    height: 3rem;
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.loading-overlay:not(.d-none) {
    animation: fadeIn 0.3s ease-out;
}
</style>

<script>
const LoadingIndicator = {
    show(message = 'กำลังดำเนินการ...') {
        const overlay = document.getElementById('loadingOverlay');
        const messageEl = overlay.querySelector('.mt-2');
        messageEl.textContent = message;
        overlay.classList.remove('d-none');
    },

    hide() {
        const overlay = document.getElementById('loadingOverlay');
        overlay.classList.add('d-none');
    }
};
</script>