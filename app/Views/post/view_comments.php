<h5 class="mb-4">ความคิดเห็น</h5>

<!-- Show Existing Comments -->
<?php if (!empty($comments)): ?>
    <?php foreach ($comments as $comment): ?>
        <?php if (empty($comment['parent_id'])): ?> <!-- Only show parent comments first -->
            <div class="comment-box mb-4 p-3 border rounded shadow-sm bg-light">
                <!-- Parent Comment -->
                <div class="parent-comment">
                    <div class="d-flex justify-content-between">
                        <div>
                            <!-- User Name with Profile Link -->
                            <strong class="text-primary">
                                <a href="<?= site_url('profile/view/' . $comment['author_id']) ?>" class="text-decoration-none text-primary">
                                    <?= esc($comment['author_name'] ?? 'ผู้เยี่ยมชม') ?>
                                </a>
                            </strong>
                            <span class="text-muted small"><?= date('d M Y H:i', strtotime($comment['created_at'])) ?></span>
                        </div>

                        <!-- Option Button for Delete -->
                        <?php if (session()->get('user_id') && (session()->get('user_id') == $comment['user_id'] || session()->get('user_role') == 'admin')): ?>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="commentOptions" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="commentOptions">
                                    <li>
                                        <form action="<?= site_url('post/deleteComment/' . $comment['id']) ?>" method="post" style="display:inline;">
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('คุณต้องการลบความคิดเห็นนี้จริงๆ หรือไม่?')">ลบความคิดเห็น</button>
                                            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>

                    <p class="mb-2"><?= esc($comment['content']) ?></p>
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
                                <div class="d-flex justify-content-between">
                                    <!-- User Name with Profile Link -->
                                    <strong class="text-success">
                                        <a href="<?= site_url('profile/view/' . esc($reply['user_id'])) ?>" class="text-decoration-none text-success">
                                            <?= esc($reply['author_name'] ?? 'ผู้เยี่ยมชม') ?>
                                        </a>
                                    </strong>
                                    <span class="text-muted small"><?= date('d M Y H:i', strtotime($reply['created_at'])) ?></span>
                                </div>
                                <p class="mb-2"><?= esc($reply['content']) ?></p>
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
        </div>
    <?php else: ?>
        <p class="text-muted">
            <a href="<?= site_url('/auth/login') ?>" class="text-decoration-none">เข้าสู่ระบบ</a> เพื่อแสดงความคิดเห็น
        </p>
    <?php endif; ?>
</div>

<!-- JavaScript -->
<script>
    // จัดการฟอร์มความคิดเห็นหลัก
    document.getElementById('commentForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const submitButton = document.getElementById('submitCommentButton');

        submitButton.disabled = true;
        submitButton.innerHTML = 'กำลังโพสต์...';

        fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('ความคิดเห็นถูกโพสต์แล้ว!');
                    form.reset();

                    let commentsContainer = document.querySelector('.comments-list');
                    if (!commentsContainer) {
                        commentsContainer = document.createElement('div');
                        commentsContainer.className = 'comments-list';
                        form.closest('.add-comment').before(commentsContainer);
                    }

                    const newComment = document.createElement('div');
                    newComment.classList.add('comment-box', 'mb-4', 'p-3', 'border', 'rounded', 'shadow-sm', 'bg-light');
                    newComment.innerHTML = `
                <div class="parent-comment">
                    <div class="d-flex justify-content-between">
                        <strong class="text-primary">
                            <a href="<?= site_url('profile/view/' . session()->get('user_id')) ?>" class="text-decoration-none text-primary">
                                ${data.comment.author_name}
                            </a>
                        </strong>
                        <span class="text-muted small">${data.comment.created_at}</span>
                    </div>
                    <p class="mb-2">${data.comment.content}</p>
                </div>
            `;
                    commentsContainer.insertBefore(newComment, commentsContainer.firstChild);
                } else {
                    alert('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถโพสต์ความคิดเห็นได้'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = 'โพสต์ความคิดเห็น';
            });
    });

    // จัดการฟอร์มความคิดเห็นตอบกลับ
    document.querySelectorAll('.replyForm').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(form);
            const submitButton = form.querySelector('button[type="submit"]');

            submitButton.disabled = true;
            submitButton.innerHTML = 'กำลังโพสต์...';

            fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert('ตอบกลับความคิดเห็นถูกโพสต์แล้ว!');
                        form.reset();

                        if (data.reply) {
                            const repliesContainer = form.closest('.comment-box').querySelector('.replies');
                            const newReply = document.createElement('div');
                            newReply.classList.add('reply', 'p-2', 'mb-2', 'border', 'rounded', 'bg-white', 'shadow-sm');
                            newReply.innerHTML = `
                        <div class="d-flex justify-content-between">
                            <strong class="text-success">
                                <a href="<?= site_url('profile/' . session()->get('user_id')) ?>" class="text-decoration-none text-success">
                                    ${data.reply.author_name}
                                </a>
                            </strong>
                            <span class="text-muted small">${data.reply.created_at}</span>
                        </div>
                        <p class="mb-2">${data.reply.content}</p>
                    `;
                            repliesContainer.appendChild(newReply);
                        }
                    } else {
                        alert('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถโพสต์ตอบกลับได้'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์');
                })
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'โพสต์ตอบกลับ';
                });
        });
    });
</script>

<style>
    .dropdown-menu {
        border-radius: 8px;
        background-color: #fff;
        border: 1px solid #ddd;
    }

    .dropdown-item {
        transition: background-color 0.3s ease;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }

    .dropdown-toggle {
        background: transparent;
        border: none;
        color: #6c757d;
        font-size: 16px;
        padding: 0;
        cursor: pointer;
    }

    .dropdown-toggle:hover {
        color: #007bff;
    }

    .comment-box p {
        font-size: 14px;
        line-height: 1.5;
    }
</style>