<!-- views/post/view_content.php -->

<div class="container my-4">
    <div class="row gx-5">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Title & Post Date -->
            <div class="post-header mb-4">
                <h1 class="fw-bold"><?= esc($post['title']) ?></h1>
                <div class="d-flex justify-content-between align-items-center text-muted small">
                    <!-- Post Date -->
                    <span>
                        <i class="far fa-calendar-alt"></i> <?= date('d M Y', strtotime($post['created_at'])) ?>
                    </span>

                    <!-- Share Buttons -->
                    <div class="d-flex align-items-center">
                        <!-- Facebook -->
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= current_url() ?>"
                            target="_blank"
                            class="text-decoration-none me-2">
                            <div class="share-icon bg-primary text-white d-flex align-items-center justify-content-center">
                                <i class="fab fa-facebook-f"></i>
                            </div>
                        </a>

                        <!-- X (Twitter) -->
                        <a href="https://twitter.com/intent/tweet?url=<?= current_url() ?>&text=<?= urlencode($post['title']) ?>"
                            target="_blank"
                            class="text-decoration-none me-2">
                            <div class="share-icon bg-info text-white d-flex align-items-center justify-content-center">
                                <i class="fa-brands fa-x-twitter"></i>
                            </div>
                        </a>

                        <!-- Copy Link -->
                        <button onclick="copyToClipboard('<?= current_url() ?>')"
                            class="text-decoration-none btn p-0">
                            <div class="share-icon bg-secondary text-white d-flex align-items-center justify-content-center">
                                <i class="fas fa-link"></i>
                            </div>
                        </button>
                    </div>
                </div>
            </div>



            <!-- JavaScript for Copy Link -->
            <script>
                function copyToClipboard(link) {
                    navigator.clipboard.writeText(link).then(() => {
                        alert('ลิงก์ถูกคัดลอกเรียบร้อยแล้ว!');
                    }).catch(err => {
                        console.error('Error copying text: ', err);
                    });
                }
            </script>

            <hr>
            <!-- Post Image -->
            <?php if ($post['image']): ?>
                <img src="<?= esc($post['image']) ?>"
                    alt="<?= esc($post['title']) ?>"
                    class="img-fluid rounded mb-4">
            <?php endif; ?>

            <!-- Breadcrumb -->

            <!-- Content -->
            <article class="mb-4">
                <?= $post['content'] // Note: Content should be sanitized before storage 
                ?>
            </article>

            <!-- Author Bio -->
            <div class="card mb-4">
                <div class="card-body d-flex align-items-center">
                    <!-- แสดง profile แก้ไม่โหลดรูป-->
                    <img src="<?= base_url('/' . (isset($post['author_avatar']) && !empty($post['author_avatar']) ? esc($post['author_avatar']) : '/avatars/default-avatar.png')) ?>"
                        alt="<?= esc($post['author_name']) ?>"
                        class="rounded-circle me-3" style="width: 64px; height: 64px;">

                    <div>
                        <!-- แสดงชื่อ user -->
                        <h5 class="card-title mb-1">

                            <a href="<?= site_url('profile/view/' . $post['author_id']) ?>"
                                class="text-decoration-none text-primary"><?= esc($post['author_name']) ?></a>

                        </h5>
                        <?php if (!empty($post['author_bio'])): ?>
                            <p class="card-text text-muted mb-0"><?= esc($post['author_bio']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>


            <!-- Reaction -->
            <div class="reaction-card card border p-3 mb-4 bg-white">
                <?= view('post/view_reaction', ['post' => $post]) ?>


                            <!-- comments -->
                <div class="comments-section border-top pt-4">
                    <?= view('post/view_comments', ['comments' => $comments]) ?>
                </div>
            </div>
            <!-- comment -->


            <!-- Sidebar -->
            <div class="col-lg-4">
                <?= view('post/sidebar', ['related_posts' => $related_posts, 'categories' => $categories, 'trending_tags' => $trending_tags]) ?>
            </div>
        </div>
    </div>

<!-- for update and toogle reaction bt -->
<script>
    function toggleReaction(postId, reactionType, event) {
    // Prevent default action (e.g., form submission or page navigation)
    if (event) {
        event.preventDefault();
    }

    // Get CSRF token from the page
    const csrfToken = document.querySelector('input[name="csrf_token_name"]')?.value;

    console.log('CSRF Token:', csrfToken);  // Log CSRF token to verify it's being fetched

    if (!csrfToken) {
        console.error('CSRF token is missing!');
        alert('CSRF token is missing. Please try again later.');
        return;
    }

    // Send request to server
    fetch(`${BASE_URL}/post/reaction`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,  // Include CSRF token in request headers
        },
        body: JSON.stringify({
            post_id: postId,
            reaction_type: reactionType,
        }),
    })
    .then(response => response.text())  // Use response.text() to check raw response
    .then(text => {
        console.log('Raw response:', text);  // Log the raw response from the server
        
        if (text.trim() === '') {
            console.error('Empty response from the server');
            alert('Server returned an empty response. Please try again later.');
            return;
        }

        try {
            const data = JSON.parse(text);  // Parse the response into JSON
            if (data.success) {
                const countElement = document.getElementById(`${reactionType}Count`);
                if (countElement) {
                    countElement.textContent = data.reactionCount;  // Update the count
                }

                // Toggle button state (active/inactive)
                const buttonId = `${reactionType}Button`;
                const buttonElement = document.getElementById(buttonId);
                if (buttonElement) {
                    buttonElement.classList.toggle('text-danger');
                }
            } else {
                console.error('Server response:', data);  // Log the server response for debugging
                alert(data.message || 'Failed to update reaction. Please try again.');
            }
        } catch (error) {
            console.error('JSON parse error:', error);  // Handle JSON parsing errors
            alert('Failed to parse server response. Please try again later.');
        }
    })
    .catch(error => {
        console.error('Network or fetch error:', error);  // Log network or fetch errors
        alert('An error occurred. Please try again later.');
    });
}
</script>