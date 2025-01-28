<div class="alert-container position-fixed top-0 end-0 p-3" style="z-index: 1050;">
    <!-- Alerts will be dynamically added here -->
</div>

<style>
.alert-container {
    max-width: 400px;
}

.alert {
    margin-bottom: 1rem;
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.alert.hiding {
    animation: slideOutRight 0.3s ease-out forwards;
}

@keyframes slideOutRight {
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

<script>
const AlertHandler = {
    show(message, type = 'success', duration = 3000) {
        const container = document.querySelector('.alert-container');
        
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        container.appendChild(alert);
        
        // Auto dismiss
        setTimeout(() => {
            alert.classList.add('hiding');
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, duration);
        
        return alert;
    },

    success(message, duration) {
        return this.show(message, 'success', duration);
    },

    error(message, duration) {
        return this.show(message, 'danger', duration);
    },

    warning(message, duration) {
        return this.show(message, 'warning', duration);
    },

    info(message, duration) {
        return this.show(message, 'info', duration);
    }
};

// Example usage:
// AlertHandler.success('บันทึกข้อมูลเรียบร้อยแล้ว');
// AlertHandler.error('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
</script>