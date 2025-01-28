const heroSlider = new Swiper('.hero-slider', {
    slidesPerView: 1,
    spaceBetween: 0,
    loop: true,
    autoplay: {
        delay: 5000,
        disableOnInteraction: false,
    },
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
});

// Load tab content
// async function loadTabContent(category) {
//     try {
//         const response = await fetch(`/api/posts/${category}`);
//         const data = await response.json();
        
//         const container = document.getElementById(`${category}Content`);
//         if (!container) return;

//         container.innerHTML = data.posts.map(post => createContentCard(post)).join('');
//     } catch (error) {
//         console.error('Error loading content:', error);
//     }
// }

// Create content card HTML
function createContentCard(post) {
    return `
        <div class="col-md-4 mb-4">
            <div class="content-card">
                <div class="content-card-image">
                    <img src="${post.image}" alt="${post.title}" class="img-fluid">
                </div>
                <div class="content-card-body">
                    <h5 class="content-card-title">${post.title}</h5>
                    <p class="content-card-text">${post.excerpt}</p>
                    
                    <div class="content-card-meta">
                        <span>
                            <i class="fas fa-user"></i>
                            ${post.author}
                        </span>
                        <span>
                            <i class="fas fa-calendar"></i>
                            ${formatDate(post.date)}
                        </span>
                    </div>
                    
                    <div class="content-card-stats">
                        <span>
                            <i class="fas fa-eye"></i>
                            ${formatNumber(post.views)}
                        </span>
                        <span>
                            <i class="fas fa-heart"></i>
                            ${formatNumber(post.likes)}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Format number with commas
function formatNumber(num) {
    return new Intl.NumberFormat('th-TH').format(num);
}

// Format date to Thai format
function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Add tab change event listeners
    const tabs = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', (event) => {
            const category = event.target.getAttribute('data-bs-target').replace('#', '');
            if (category !== 'trending') {
                loadTabContent(category);
            }
        });
    });
});


// Main JavaScript Functions

// Initialize Tooltips
const initTooltips = () => {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
};

// Initialize Popovers
const initPopovers = () => {
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
};

// Handle Notifications
const NotificationHandler = {
    init() {
        this.unreadCount = parseInt(localStorage.getItem('unreadNotifications')) || 0;
        this.updateBadge();
        this.setupWebSocket();
        this.setupMarkAsRead();
    },

    updateBadge() {
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            badge.textContent = this.unreadCount;
            badge.style.display = this.unreadCount > 0 ? 'inline' : 'none';
        }
    },

    setupWebSocket() {
        if ('WebSocket' in window) {
            const ws = new WebSocket(`ws://${window.location.host}/notifications`);
            
            ws.onmessage = (event) => {
                const data = JSON.parse(event.data);
                this.handleNewNotification(data);
            };

            ws.onerror = (error) => {
                console.error('WebSocket Error:', error);
            };
        }
    },

    handleNewNotification(notification) {
        this.unreadCount++;
        this.updateBadge();
        this.showNotificationToast(notification);
    },

    showNotificationToast(notification) {
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = `
            <div class="toast-header">
                <strong class="me-auto">${notification.title}</strong>
                <small>${notification.time}</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${notification.message}
            </div>
        `;

        document.getElementById('toastContainer').appendChild(toast);
        new bootstrap.Toast(toast).show();
    },

    setupMarkAsRead() {
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', async (e) => {
                e.preventDefault();
                const id = item.dataset.id;
                
                try {
                    const response = await fetch(`${baseUrl}/notifications/markAsRead/${id}`, {
                        method: 'POST'
                    });
                    
                    if (response.ok) {
                        item.classList.remove('unread');
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                        this.updateBadge();
                    }
                } catch (error) {
                    console.error('Error marking notification as read:', error);
                }
            });
        });
    }
};

// Handle File Uploads
const FileUploader = {
    init(options = {}) {
        this.options = {
            acceptTypes: ['image/jpeg', 'image/png', 'image/gif'],
            maxSize: 5 * 1024 * 1024, // 5MB
            ...options
        };

        this.setupDropZone();
    },

    setupDropZone() {
        const dropZones = document.querySelectorAll('.dropzone-container');
        
        dropZones.forEach(zone => {
            zone.addEventListener('dragover', (e) => {
                e.preventDefault();
                zone.classList.add('dragover');
            });

            zone.addEventListener('dragleave', () => {
                zone.classList.remove('dragover');
            });

            zone.addEventListener('drop', (e) => {
                e.preventDefault();
                zone.classList.remove('dragover');
                this.handleFiles(e.dataTransfer.files, zone);
            });

            // Click to upload
            const input = zone.querySelector('input[type="file"]');
            if (input) {
                zone.addEventListener('click', () => input.click());
                input.addEventListener('change', () => {
                    this.handleFiles(input.files, zone);
                });
            }
        });
    },

    handleFiles(files, zone) {
        Array.from(files).forEach(file => {
            if (!this.validateFile(file)) return;
            
            this.uploadFile(file, zone);
        });
    },

    validateFile(file) {
        if (!this.options.acceptTypes.includes(file.type)) {
            alert('ประเภทไฟล์ไม่ถูกต้อง');
            return false;
        }

        if (file.size > this.options.maxSize) {
            alert('ขนาดไฟล์ใหญ่เกินไป');
            return false;
        }

        return true;
    },

    async uploadFile(file, zone) {
        const formData = new FormData();
        formData.append('file', file);

        try {
            const response = await fetch(`${baseUrl}/upload`, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            
            if (result.success) {
                this.showPreview(result.url, zone);
            } else {
                alert(result.message || 'อัพโหลดไม่สำเร็จ');
            }
        } catch (error) {
            console.error('Upload error:', error);
            alert('เกิดข้อผิดพลาดในการอัพโหลด');
        }
    },

    showPreview(url, zone) {
        const preview = zone.querySelector('.preview') || document.createElement('div');
        preview.className = 'preview mt-3';
        
        const img = document.createElement('img');
        img.src = url;
        img.className = 'img-thumbnail';
        
        preview.appendChild(img);
        zone.appendChild(preview);
    }
};

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', () => {
    initTooltips();
    initPopovers();
    NotificationHandler.init();
    
    // Initialize FileUploader where needed
    if (document.querySelector('.dropzone-container')) {
        FileUploader.init();
    }

    // Scroll to top button
    const scrollTopBtn = document.getElementById('scrollTopBtn');
    if (scrollTopBtn) {
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 100) {
                scrollTopBtn.style.display = 'block';
            } else {
                scrollTopBtn.style.display = 'none';
            }
        });

        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});