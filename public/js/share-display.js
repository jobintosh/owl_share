/**
 * ShareDisplay - จัดการการแสดงผลในหน้า Share
 */
const ShareDisplay = {
    currentPage: 1,
    loading: false,
    hasMore: true,

    init() {
        this.container = document.getElementById('recentPosts');
        if (!this.container) return;

        this.setupInfiniteScroll();
        this.setupEventListeners();
    },

    /**
     * ตั้งค่า Infinite Scroll
     */
    setupInfiniteScroll() {
        const options = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !this.loading && this.hasMore) {
                    this.loadMore();
                }
            });
        }, options);

        // Observe loading trigger
        const trigger = document.createElement('div');
        trigger.id = 'loadingTrigger';
        this.container.appendChild(trigger);
        observer.observe(trigger);
    },

    /**
     * ตั้งค่า Event Listeners
     */
    setupEventListeners() {
        // Refresh button
        document.querySelector('.refresh-posts')?.addEventListener('click', () => {
            this.refresh();
        });

        // Like buttons
        this.container.addEventListener('click', (e) => {
            const likeBtn = e.target.closest('.like-button');
            if (likeBtn) {
                const postId = likeBtn.dataset.postId;
                this.handleLike(postId);
            }
        });

        // Comment sections
        this.container.addEventListener('click', (e) => {
            const commentBtn = e.target.closest('.comment-button');
            if (commentBtn) {
                const postId = commentBtn.dataset.postId;
                this.toggleComments(postId);
            }
        });

        // Share buttons
        this.container.addEventListener('click', (e) => {
            const shareBtn = e.target.closest('.share-button');
            if (shareBtn) {
                const postId = shareBtn.dataset.postId;
                const title = shareBtn.dataset.title;
                this.sharePost(postId, title);
            }
        });
    },

    /**
     * โหลดโพสต์เพิ่มเติม
     */
    async loadMore() {
        if (this.loading || !this.hasMore) return;

        this.loading = true;
        this.showLoading(true);

        try {
            const response = await fetch(`${baseUrl}/share/getPosts?page=${this.currentPage + 1}`);
            const data = await response.json();

            if (data.success && data.posts.length > 0) {
                this.appendPosts(data.posts);
                this.currentPage++;
                this.hasMore = data.has_more;
            } else {
                this.hasMore = false;
            }
        } catch (error) {
            console.error('Error loading posts:', error);
            this.showError('ไม่สามารถโหลดโพสต์เพิ่มเติมได้');
        } finally {
            this.loading = false;
            this.showLoading(false);
        }
    },

    /**
     * รีเฟรชโพสต์
     */
    async refresh() {
        this.showLoading(true);
        this.container.innerHTML = '';

        try {
            const response = await fetch(`${baseUrl}/share/getPosts?page=1`);
            const data = await response.json();

            if (data.success) {
                this.appendPosts(data.posts);
                this.currentPage = 1;
                this.hasMore = data.has_more;
            }
        } catch (error) {
            console.error('Error refreshing posts:', error);
            this.showError('ไม่สามารถรีเฟรชโพสต์ได้');
        } finally {
            this.showLoading(false);
        }
    },

    /**
     * เพิ่มโพสต์ในหน้า
     */
    appendPosts(posts) {
        posts.forEach(post => {
            const postElement = this.createPostElement(post);
            this.container.insertBefore(postElement, document.getElementById('loadingTrigger'));
        });
    },

    /**
     * สร้าง Element ของโพสต์
     */
    createPostElement(post) {
        const div = document.createElement('div');
        div.className = 'shared-item';
        div.setAttribute('data-post-id', post.id);

        div.innerHTML = `
            <div class="post-header d-flex justify-content-between align-items-start">
                <div class="d-flex align-items-center">
                    <img src="${post.author_avatar || '/images/default-avatar.png'}" 
                         class="avatar me-2" alt="${this.escapeHtml(post.author_name)}">
                    <div>
                        <h5 class="post-title mb-1">${this.escapeHtml(post.title)}</h5>
                        <div class="post-meta">
                            <span class="author">${this.escapeHtml(post.author_name)}</span>
                            <span class="date ms-2">${this.formatDate(post.created_at)}</span>
                        </div>
                    </div>
                </div>
                ${this.createPostMenu(post)}
            </div>

            <div class="post-content mt-3">
                ${this.renderPostContent(post)}
            </div>

            <div class="post-footer mt-3">
                <div class="post-tags mb-2">
                    <span class="category-badge">
                        <i class="fas fa-folder"></i> ${this.escapeHtml(post.category_name)}
                    </span>
                    ${this.renderTags(post.tags)}
                </div>

                <div class="post-actions">
                    <button class="btn btn-sm ${post.liked ? 'btn-primary' : 'btn-outline-primary'} like-button"
                            data-post-id="${post.id}">
                        <i class="fas fa-heart"></i>
                        <span class="like-count">${post.like_count}</span>
                    </button>

                    <button class="btn btn-sm btn-outline-primary ms-2 comment-button"
                            data-post-id="${post.id}">
                        <i class="fas fa-comment"></i>
                        <span class="comment-count">${post.comment_count}</span>
                    </button>

                    <button class="btn btn-sm btn-outline-primary ms-2 share-button"
                            data-post-id="${post.id}"
                            data-title="${this.escapeHtml(post.title)}">
                        <i class="fas fa-share"></i> แชร์
                    </button>
                </div>

                <div id="comments-${post.id}" class="comments-section mt-3" style="display: none;">
                    <!-- Comments will be loaded here -->
                </div>
            </div>
        `;

        return div;
    },

    /**
     * สร้างเมนูของโพสต์
     */
    createPostMenu(post) {
        if (userId !== post.author_id) return '';

        return `
            <div class="dropdown">
                <button class="btn btn-link text-muted" data-bs-toggle="dropdown">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="${baseUrl}/share/edit/${post.id}">
                            <i class="fas fa-edit"></i> แก้ไข
                        </a>
                    </li>
                    <li>
                        <button class="dropdown-item text-danger" 
                                onclick="ShareDisplay.deletePost(${post.id})">
                            <i class="fas fa-trash"></i> ลบ
                        </button>
                    </li>
                </ul>
            </div>
        `;
    },

    /**
     * แสดงเนื้อหาโพสต์ตามประเภท
     */
    renderPostContent(post) {
        switch (post.type) {
            case 'text':
                return post.content;

            case 'whiteboard':
                return `
                    <img src="${baseUrl}/${post.content}" 
                         alt="Whiteboard" 
                         class="img-fluid rounded cursor-pointer"
                         onclick="ShareDisplay.viewImage(this.src)">
                `;

            case 'gallery':
                const images = JSON.parse(post.content);
                return `
                    <div class="gallery-grid">
                        ${images.map(image => `
                            <div class="gallery-item">
                                <img src="${baseUrl}/${image}" 
                                     alt="Gallery Image"
                                     loading="lazy"
                                     onclick="ShareDisplay.viewImage(this.src)"
                                     class="cursor-pointer">
                            </div>
                        `).join('')}
                    </div>
                `;

            default:
                return `<div class="text-muted">ไม่พบเนื้อหา</div>`;
        }
    },

    /**
     * แสดง Tags
     */
    renderTags(tags) {
        if (!tags) return '';
        
        try {
            const tagArray = JSON.parse(tags);
            return tagArray.map(tag => `
                <a href="${baseUrl}/search?tag=${encodeURIComponent(tag)}" 
                   class="tag-badge text-decoration-none">
                    <i class="fas fa-tag"></i> ${this.escapeHtml(tag)}
                </a>
            `).join('');
        } catch {
            return '';
        }
    },

    /**
     * จัดการการกดไลค์
     */
    async handleLike(postId) {
        if (!userId) {
            alert('กรุณาเข้าสู่ระบบก่อนกดถูกใจ');
            return;
        }

        try {
            const response = await fetch(`${baseUrl}/share/like/${postId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            if (data.success) {
                this.updateLikeButton(postId, data.liked, data.like_count);
            }
        } catch (error) {
            console.error('Error toggling like:', error);
            this.showError('ไม่สามารถกดถูกใจได้');
        }
    },

    /**
     * อัพเดทปุ่มไลค์
     */
    updateLikeButton(postId, liked, count) {
        const button = this.container.querySelector(`.like-button[data-post-id="${postId}"]`);
        if (!button) return;

        button.classList.toggle('btn-primary', liked);
        button.classList.toggle('btn-outline-primary', !liked);
        button.querySelector('.like-count').textContent = count;
    },

    /**
     * ลบโพสต์
     */
    deletePost(postId) {
        if (confirm('คุณต้องการลบโพสต์นี้ใช่หรือไม่?')) {
            this.performDelete(postId);
        }
    },

    /**
     * ดำเนินการลบโพสต์
     */
    async performDelete(postId) {
        try {
            const response = await fetch(`${baseUrl}/share/delete/${postId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            if (data.success) {
                const postElement = this.container.querySelector(`[data-post-id="${postId}"]`);
                if (postElement) {
                    postElement.remove();
                }
            } else {
                this.showError(data.message || 'ไม่สามารถลบโพสต์ได้');
            }
        } catch (error) {
            console.error('Error deleting post:', error);
            this.showError('เกิดข้อผิดพลาดในการลบโพสต์');
        }
    },

    /**
     * Helper Functions
     */
    showLoading(show) {
        const trigger = document.getElementById('loadingTrigger');
        if (trigger) {
            trigger.innerHTML = show ? `
                <div class="text-center py-3">
                    <div class="spinner-border spinner-border-sm text-primary"></div>
                    <div class="mt-2">กำลังโหลด...</div>
                </div>
            ` : '';
        }
    },

    showError(message) {
        alert(message);
    },

    escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    },

    formatDate(date) {
        return new Date(date).toLocaleDateString('th-TH', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    ShareDisplay.init();
});