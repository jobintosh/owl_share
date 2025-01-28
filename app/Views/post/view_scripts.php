<?php
// views/post/view_scripts.php
?>
<script>
// Toggle Like
async function toggleLike(postId) {
    try {
        const response = await fetch(`<?= site_url('post/like/') ?>${postId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            const likeButton = document.getElementById('likeButton');
            const likeCount = document.getElementById('likeCount');
            const heartIcon = likeButton.querySelector('i');
            
            if (data.liked) {
                likeButton.classList.remove('text-gray-500');
                likeButton.classList.add('text-red-500');
                heartIcon.classList.remove('far');
                heartIcon.classList.add('fas');
            } else {
                likeButton.classList.remove('text-red-500');
                likeButton.classList.add('text-gray-500');
                heartIcon.classList.remove('fas');
                heartIcon.classList.add('far');
            }
            
            likeCount.textContent = data.like_count;
        } else {
            if (data.message.includes('เข้าสู่ระบบ')) {
                window.location.href = `<?= site_url('login') ?>?redirect=${encodeURIComponent(window.location.href)}`;
            } else {
                alert(data.message);
            }
        }
    } catch (error) {
        console.error('Error:', error);
        alert('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
    }
}

// Submit Comment
async function submitComment(event) {
    event.preventDefault();
    
    const content = document.getElementById('commentContent').value.trim();
    const postId = document.getElementById('postId').value;
    const parentId = document.getElementById('parentCommentId')?.value;
    
    if (!content) {
        alert('กรุณาใส่ความคิดเห็น');
        return;
    }
    
    try {
        const response = await fetch(`<?= site_url('post/comment/') ?>${postId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            body: JSON.stringify({ content, parent_id: parentId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // เพิ่มความคิดเห็นใหม่ลงในหน้า
            addCommentToList(data.comment);
            
            // ล้างฟอร์ม
            document.getElementById('commentContent').value = '';
            if (parentId) {
                cancelReply();
            }
        } else {
            alert(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
    }
}

// Reply to Comment
function replyToComment(commentId) {
    const commentForm = document.querySelector('.comment-form');
    const replyTo = document.getElementById(`comment-${commentId}`);
    
    // สร้าง input hidden สำหรับเก็บ parent_id
    if (!document.getElementById('parentCommentId')) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.id = 'parentCommentId';
        commentForm.appendChild(input);
    }
    
    document.getElementById('parentCommentId').value = commentId;
    document.getElementById('commentContent').placeholder = 'ตอบกลับความคิดเห็น...';
    
    // เพิ่มปุ่มยกเลิก
    if (!document.querySelector('.cancel-reply')) {
        const cancelBtn = document.createElement('button');
        cancelBtn.type = 'button';
        cancelBtn.className = 'cancel-reply px-4 py-2 text-gray-600 hover:text-gray-800';
        cancelBtn.textContent = 'ยกเลิก';
        cancelBtn.onclick = cancelReply;
        document.querySelector('.comment-form .flex').appendChild(cancelBtn);
    }
    
    // เลื่อนฟอร์มไปไว้ใต้ความคิดเห็นที่ต้องการตอบ
    replyTo.appendChild(commentForm);
    document.getElementById('commentContent').focus();
}

// Cancel Reply
function cancelReply() {
    const commentForm = document.querySelector('.comment-form');
    const originalPosition = document.getElementById('comments');
    
    // ย้ายฟอร์มกลับตำแหน่งเดิม
    originalPosition.insertBefore(commentForm, originalPosition.firstChild);
    
    // รีเซ็ตฟอร์ม
    document.getElementById('parentCommentId')?.remove();
    document.getElementById('commentContent').placeholder = 'แสดงความคิดเห็น...';
    document.querySelector('.cancel-reply')?.remove();
}

// Delete Comment
async function deleteComment(commentId) {
    if (!confirm('คุณแน่ใจหรือไม่ที่จะลบความคิดเห็นนี้?')) {
        return;
    }
    
    try {
        const response = await fetch(`<?= site_url('comment/delete/') ?>${commentId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            document.getElementById(`comment-${commentId}`).remove();
        } else {
            alert(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
    }
}

// Add Comment to List
function addCommentToList(comment) {
    const commentsList = document.getElementById('commentsList');
    const commentTemplate = `
        <div class="mb-6 comment-item" id="comment-${comment.id}">
            <div class="flex space-x-4">
                <div class="flex-shrink-0">
                    <img src="${comment.author_avatar || '/images/default-avatar.png'}" 
                         alt="${comment.author_name}"
                         class="w-10 h-10 rounded-full">
                </div>
                <div class="flex-grow">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <a href="<?= site_url('profile/') ?>${comment.author_username}" 
                               class="font-semibold hover:text-blue-600">
                                ${comment.author_name}
                            </a>
                            <span class="text-sm text-gray-500">เมื่อสักครู่</span>
                        </div>
                        <div class="prose prose-sm">
                            ${comment.content}
                        </div>
                    </div>
                    <div class="mt-2 ml-4 flex items-center space-x-4 text-sm">
                        <button onclick="replyToComment(${comment.id})" 
                                class="text-gray-500 hover:text-blue-600">
                            <i class="far fa-comment-dots mr-1"></i>
                            ตอบกลับ
                        </button>
                        <button onclick="deleteComment(${comment.id})" 
                                class="text-gray-500 hover:text-red-600">
                            <i class="far fa-trash-alt mr-1"></i>
                            ลบ
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    if (comment.parent_id) {
        const parentComment = document.getElementById(`comment-${comment.parent_id}`);
        let repliesContainer = parentComment.querySelector('.replies');
        
        if (!repliesContainer) {
            repliesContainer = document.createElement('div');
            repliesContainer.className = 'ml-8 mt-4 space-y-4 replies';
            parentComment.querySelector('.flex-grow').appendChild(repliesContainer);
        }
        
        repliesContainer.insertAdjacentHTML('beforeend', commentTemplate);
    } else {
        commentsList.insertAdjacentHTML('afterbegin', commentTemplate);
    }
}

// Copy URL to Clipboard
function copyToClipboard(url) {
    navigator.clipboard.writeText(url).then(() => {
        alert('คัดลอก URL เรียบร้อยแล้ว');
    }).catch(() => {
        alert('ไม่สามารถคัดลอก URL ได้');
    });
}
</script>