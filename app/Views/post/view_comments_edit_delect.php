<h5 class="mb-4">ความคิดเห็น</h5>

<!-- Show Existing Comments -->
<?php if (!empty($comments)): ?>
    <?php foreach ($comments as $comment): ?>
        <?php if (empty($comment['parent_id'])): ?> <!-- Only show parent comments first -->
            <div class="comment-box mb-4 p-3 border rounded shadow-sm bg-light">
                <!-- Parent Comment -->
                <div class="parent-comment">
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- User Name with Profile Link -->
                        <div>
                            <strong class="text-primary">
                                <a href="<?= site_url('profile/view/' . $comment['author_id']) ?>" class="text-decoration-none text-primary">
                                    <?= esc($comment['author_name'] ?? 'ผู้เยี่ยมชม') ?>
                                </a>
                            </strong>
                            <span class="text-muted small ms-2"><?= date('d M Y H:i', strtotime($comment['created_at'])) ?></span>
                        </div>

                        <!-- แสดงปุ่มแก้ไข/ลบสำหรับเจ้าของหรือ Admin -->
                        <?php if (session()->get('user_id') == $comment['author_id'] || $isAdmin): ?>
                            <div class="comment-actions">
                                <button 
                                    class="btn btn-sm btn-outline-secondary edit-comment-btn" 
                                    data-comment-id="<?= $comment['id'] ?>"
                                    data-comment-content="<?= esc($comment['content']) ?>"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button 
                                    class="btn btn-sm btn-outline-danger delete-comment-btn ms-1" 
                                    data-comment-id="<?= $comment['id'] ?>"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>

                    <p class="mb-2 mt-2 comment-content"><?= esc($comment['content']) ?></p>

                    <?php if ($comment['status'] === 'pending'): ?>
                        <span class="badge bg-warning text-dark">(รอการอนุมัติ)</span>
                    <?php endif; ?>

                    <!-- Reply Button (แสดงเฉพาะผู้ล็อกอิน) -->
                    <?php if (session()->get('user_id')): ?>
                        <button class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="collapse" data-bs-target="#replyForm<?= $comment['id'] ?>">
                            ตอบกลับ
                        </button>
                    <?php else: ?>
                        <p class="text-muted mt-2 small">
                            <a href="<?= site_url('/auth/login') ?>" class="text-decoration-none">เข้าสู่ระบบ</a> เพื่อตอบกลับความคิดเห็น
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Reply Form (แสดงเฉพาะผู้ล็อกอิน) -->
                <?php if (session()->get('user_id')): ?>
                    <div id="replyForm<?= $comment['id'] ?>" class="collapse mt-3">
                        <form action="<?= site_url('post/addCommentReply/' . $comment['id']) ?>" method="post" class="replyForm">
                            <div class="mb-2">
                                <textarea name="content" class="form-control" rows="2" placeholder="ตอบกลับความคิดเห็นนี้..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary">โพสต์ตอบกลับ</button>
                            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                        </form>
                    </div>
                <?php endif; ?>

                <!-- Replies for this Comment -->
                <div class="replies mt-3">
                    <?php foreach ($comments as $reply): ?>
                        <?php if ($reply['parent_id'] == $comment['id']): ?>
                            <div class="reply p-2 mb-2 border rounded bg-white shadow-sm">
                                <div class="d-flex justify-content-between align-items-center">
                                    <!-- User Name with Profile Link -->
                                    <div>
                                        <strong class="text-success">
                                            <a href="<?= site_url('profile/' . esc($reply['user_id'])) ?>" class="text-decoration-none text-success">
                                                <?= esc($reply['author_name'] ?? 'ผู้เยี่ยมชม') ?>
                                            </a>
                                        </strong>
                                        <span class="text-muted small ms-2"><?= date('d M Y H:i', strtotime($reply['created_at'])) ?></span>
                                    </div>

                                    <!-- แสดงปุ่มแก้ไข/ลบสำหรับเจ้าของหรือ Admin -->
                                    <?php if (session()->get('user_id') == $reply['author_id'] || $isAdmin): ?>
                                        <div class="comment-actions">
                                            <button 
                                                class="btn btn-sm btn-outline-secondary edit-comment-btn" 
                                                data-comment-id="<?= $reply['id'] ?>"
                                                data-comment-content="<?= esc($reply['content']) ?>"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button 
                                                class="btn btn-sm btn-outline-danger delete-comment-btn ms-1" 
                                                data-comment-id="<?= $reply['id'] ?>"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />

                                        </div>
                                    <?php endif; ?>
                                </div>
                                <p class="mb-2 mt-2 comment-content"><?= esc($reply['content']) ?></p>
                                <?php if ($reply['status'] === 'pending'): ?>
                                    <span class="badge bg-warning text-dark">(รอการอนุมัติ)</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php else: ?>
    <p class="text-muted">ยังไม่มีความคิดเห็น</p>
<?php endif; ?>

<!-- Add Comment Box -->
<div class="add-comment mt-4">
    <?php if (session()->get('user_id')): ?>
        <div class="p-3 border rounded bg-light shadow-sm">
            <h6 class="text-primary">เพิ่มความคิดเห็นของคุณ</h6>
            <form id="commentForm" action="<?= site_url('post/addComment/' . $post['id']) ?>" method="post">
                <!-- CSRF Token -->
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />

                <div class="mb-3">
                    <textarea name="content" class="form-control" rows="3" placeholder="เขียนความคิดเห็นของคุณ..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary" id="submitCommentButton">โพสต์ความคิดเห็น</button>
            </form>
            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />

        </div>
    <?php else: ?>
        <p class="text-muted">
            <a href="<?= site_url('/auth/login') ?>" class="text-decoration-none">เข้าสู่ระบบ</a> เพื่อแสดงความคิดเห็น
        </p>
    <?php endif; ?>
</div>

<!-- JavaScript -->
<script>
   // ================== จัดการการแก้ไขความคิดเห็น ==================
document.querySelectorAll('.edit-comment-btn').forEach(button => {
    button.addEventListener('click', function() {
        const commentId = this.dataset.commentId;
        const originalContent = this.dataset.commentContent;
        const commentBox = this.closest('.comment-box, .reply');
        const contentElement = commentBox.querySelector('.comment-content');

        // สร้างฟอร์มแก้ไข
        const editForm = document.createElement('form');
        editForm.className = 'edit-comment-form mb-2';
        editForm.innerHTML = `
            <textarea class="form-control mb-2" rows="3">${originalContent}</textarea>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary">บันทึก</button>
                <button type="button" class="btn btn-sm btn-secondary cancel-edit">ยกเลิก</button>
            </div>
        `;

        // แทนที่เนื้อหาเดิมด้วยฟอร์มแก้ไข
        contentElement.replaceWith(editForm);

        // จัดการการยกเลิก
        editForm.querySelector('.cancel-edit').addEventListener('click', () => {
            editForm.replaceWith(contentElement);
        });

        // จัดการการบันทึก
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const newContent = this.querySelector('textarea').value.trim();

            if (newContent === originalContent) {
                editForm.replaceWith(contentElement);
                return;
            }

            fetch(`<?= site_url('post/updateComment/') ?>${commentId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="csrf_token_name"]').value
                },
                body: JSON.stringify({ content: newContent }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    contentElement.textContent = newContent;
                    editForm.replaceWith(contentElement);
                } else {
                    alert('เกิดข้อผิดพลาด: ' + data.message);
                }
            });
        });
    });
});

// ================== จัดการการลบความคิดเห็น ==================
document.querySelectorAll('.delete-comment-btn').forEach(button => {
    button.addEventListener('click', function() {
        const commentId = this.dataset.commentId;
        const commentBox = this.closest('.comment-box, .reply');

        if (confirm('คุณแน่ใจว่าต้องการลบความคิดเห็นนี้?')) {
            fetch(`<?= site_url('post/deleteComment/') ?>${commentId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="csrf_token_name"]').value
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    commentBox.remove();
                } else {
                    alert('เกิดข้อผิดพลาด: ' + data.message);
                }
            });
        }
    });
});
</script>