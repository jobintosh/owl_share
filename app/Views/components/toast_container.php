<div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastContainer">
    <!-- Toasts will be dynamically added here -->
</div>

<style>
.toast {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    min-width: 300px;
}

.toast-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.toast-body {
    padding: 0.75rem;
}

.notification-toast {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.toast.hide {
    animation: slideOut 0.3s ease-out forwards;
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}
</style>