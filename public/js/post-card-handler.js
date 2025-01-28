/**
 * Post Card Handler
 */
const PostCard = {
    async toggleLike(postId) {
        if (!userId) {
            AlertHandler.error('กรุณาเข้าสู่ระบบก่อนกดถูกใจ');
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
            } else {
                AlertHandler.error(data.message);
            }
        } catch (error) {
            console.error('Error toggling like:', error);
            AlertHandler.error('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
        }
    },

    updateLikeButton(postId, liked, count) {
        const btn = document.querySelector(`#post-${postId} .btn-sm[onclick*="toggleLike"]`);
        const countSpan = btn.querySelector('.like-count');
        
        btn.classList.toggle('btn-primary', liked);
        btn.classList.toggle('btn-outline-primary', !liked);
        countSpan.textContent = count.toLocaleString();

        // Animate heart icon
        const icon = btn.querySelector('.fa-heart');
        icon.classList.add('animate__animated', 'animate__heartBeat');
        setTimeout(() => {
            icon.classList.remove('animate__animated', 'animate__heartBeat');
        }, 1000);
    },

    async toggleComments(postId) {
        const commentsSection = document.getElementById(`comments-${postId}`);
        const isHidden = commentsSection.style.display === 'none';

        if (isHidden) {
            commentsSection.style.display = 'block';
            await this.loadComments(postId);
        } else {
            commentsSection.style.display = 'none';
        }
    },

    async loadComments(postId) {
        const section = document.getElementById(`comments-${postId}`);
        section.innerHTML = `
            <div class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary"></div>
                <p class="mb-0 mt-2">กำลังโหลดความคิดเห็น...</p>
            </div>
        `;

        try {
            const response = await fetch(`${baseUrl}/share/comments/${postId}`);
            const data = await response.json();

            if (data.success) {
                section.innerHTML = this.renderComments(data.comments, postId);
                this.initCommentForm(postId);
            } else {
                section.innerHTML = `
                    <div class="text-center text-danger py-3">
                        <i class="fas fa-exclamation-circle"></i>
                        <p class="mb-0 mt-2">${data.message}</p>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error loading comments:', error);
            section.innerHTML = `
                <div class="text-center text-danger py-3">
                    <i class="fas fa-exclamation-circle"></i>
                    <p class="mb-0 mt-2">ไม่สามารถโหลดความคิดเห็นได้</p>
                </div>
            `;
        }
    },

    renderComments(comments, postId) {
        let html = `
            <div class="comments-list mb-3">
                ${comments.length ? '' : '<p class="text-center text-muted">ยังไม่มีความคิดเห็น</p>'}
                ${comments.map(comment => this.renderComment(comment)).join('')}
            </div>
            
            ${userId ? `
                <form class="comment-form" onsubmit="return PostCard.submitComment(event, ${postId})">
                    <div class="input-group">
                        <input type="text" class="form-control" 
                               placeholder="เขียนความคิดเห็น..." required>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            ` : `
                <p class="text-center mb-0">
                    <a href="${baseUrl}/login" class="text-primary">เข้าสู่ระบบ</a> 
                    เพื่อแสดงความคิดเห็น
                </p>
            `}
        `;

        return html;
    },

    renderComment(comment) {
        return `
            <div class="comment-item ${comment.parent_id ? 'ms-4' : ''} mb-3">
                <div class="d-flex">
                    <img src="${comment.author_avatar || baseUrl + '/images/default-avatar.png'}" 
                         class="author-avatar me-2" alt="${comment.author_name}">
                    <div class="flex-grow-1">
                        <div class="comment-header">
                            <strong>${comment.author_name}</strong>
                            <small class="text-muted ms-2">
                                ${this.formatDate(comment.created_at)}
                            </small>
                        </div>
                        <div class="comment-content mt-1">
                            ${comment.content}
                        </div>
                        <div class="comment-actions mt-1">
                            <button class="btn btn-link btn-sm p-0" 
                                    onclick="PostCard.replyToComment(${comment.id})">
                                <i class="fas fa-reply"></i> ตอบกลับ
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    },

    async submitComment(event, postId) {
        event.preventDefault();
        
        if (!userId) {
            AlertHandler.error('กรุณาเข้าสู่ระบบก่อนแสดงความคิดเห็น');
            return false;
        }

        const form = event.target;
        const input = form.querySelector('input');
        const content = input.value.trim();
        
        if (!content) return false;

        try {
            const response = await fetch(`${baseUrl}/share/comment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ post_id: postId, content })
            });

            const data = await response.json();
            if (data.success) {
                input.value = '';
                await this.loadComments(postId);
                this.updateCommentCount(postId, data.comment_count);
            } else {
                AlertHandler.error(data.message);
            }
        } catch (error) {
            console.error('Error submitting comment:', error);
            AlertHandler.error('ไม่สามารถบันทึกความคิดเห็นได้');
        }

        return false;
    },

    updateCommentCount(postId, count) {
        const countSpan = document.querySelector(`#post-${postId} .comment-count`);
        if (countSpan) {
            countSpan.textContent = count.toLocaleString();
        }
    },

    async delete(postId) {
        ModalHandler.confirm('คุณต้องการลบโพสต์นี้หรือไม่?', async () => {
            try {
                const response = await fetch(`${baseUrl}/share/delete/${postId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    const postElement = document.getElementById(`post-${postId}`);
                    postElement.classList.add('animate__animated', 'animate__fadeOut');
                    setTimeout(() => postElement.remove(), 500);
                    AlertHandler.success('ลบโพสต์เรียบร้อยแล้ว');
                } else {
                    AlertHandler.error(data.message);
                }
            } catch (error) {
                console.error('Error deleting post:', error);
                AlertHandler.error('ไม่สามารถลบโพสต์ได้');
            }
        });
    },

    share(postId) {
        const url = `${baseUrl}/post/${postId}`;
        const title = document.querySelector(`#post-${postId} h5`).textContent;
        
        ModalHandler.initShareModal(url, title);
        new bootstrap.Modal(document.getElementById('shareModal')).show();
    },

    viewImage(src) {
        ModalHandler.showImagePreview(src);
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

// Add necessary styles
const style = document.createElement('style');
style.textContent = `
    .comment-item {
        padding: 0.75rem;
        border-radius: 0.5rem;
        background: white;
        margin-bottom: 0.5rem;
    }

    .comment-item:hover {
        background: #f8f9fa;
    }

    .comment-form .input-group {
        background: white;
        border-radius: 1.5rem;
        overflow: hidden;
    }

    .comment-form input {
        border: none;
        padding: 0.75rem 1rem;
    }

    .comment-form button {
        border-radius: 0;
        padding: 0.75rem 1.5rem;
    }

    .animate__fadeOut {
        animation: fadeOut 0.5s ease forwards;
    }

    @keyframes fadeOut {
        to {
            opacity: 0;
            transform: translateY(-20px);
        }
    }
`;
document.head.appendChild(style);