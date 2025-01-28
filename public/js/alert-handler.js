// public/js/alert-handler.js
const AlertHandler = {
    success(message) {
        this.show('success', message);
    },
    
    error(message) {
        this.show('danger', message); 
    },
    
    warning(message) {
        this.show('warning', message);
    },
    
    info(message) {
        this.show('info', message);
    },
    
    show(type, message) {
        // สร้าง Alert Element
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
        alertDiv.style.zIndex = '9999';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // เพิ่มเข้าไปใน DOM
        document.body.appendChild(alertDiv);
        
        // ซ่อนอัตโนมัติหลัง 3 วินาที
        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 150);
        }, 3000);
    }
};