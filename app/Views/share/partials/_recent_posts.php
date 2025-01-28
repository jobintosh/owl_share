<?php
/**
 * Recent Posts Partial
 */
?>
<div class="card">
    <div class="card-body">
        <h5 class="card-title">
            <i class="fas fa-clock"></i> โพสต์ล่าสุด
            <button type="button" class="btn btn-outline-primary btn-sm float-end" 
                    onclick="RecentPosts.refresh()">
                <i class="fas fa-sync-alt"></i> รีเฟรช
            </button>
        </h5>

        <!-- Posts Container -->
        <div id="recentPosts">
            <?php if (!empty($recent_posts)): ?>
                <?php foreach ($recent_posts as $post): ?>
                    <?= view('share/partials/_post_card', ['post' => $post]) ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center text-muted py-5">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>ยังไม่มีโพสต์</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Load More Button -->
        <?php if (count($recent_posts) >= 5): ?>
            <div class="text-center mt-3">
                <button type="button" class="btn btn-outline-primary" 
                        onclick="RecentPosts.loadMore()">
                    <i class="fas fa-plus"></i> โหลดเพิ่มเติม
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
const RecentPosts = {
    page: 1,
    loading: false,
    hasMore: true,

    async loadMore() {
        if (this.loading || !this.hasMore) return;

        this.loading = true;
        this.updateLoadMoreButton(true);

        try {
            const response = await fetch(`${baseUrl}/share/getPosts?page=${this.page + 1}`);
            const data = await response.json();

            if (data.posts && data.posts.length > 0) {
                this.appendPosts(data.posts);
                this.page++;
                this.hasMore = data.has_more;
            } else {
                this.hasMore = false;
            }
        } catch (error) {
            console.error('Error loading posts:', error);
            AlertHandler.error('ไม่สามารถโหลดโพสต์เพิ่มเติมได้');
        } finally {
            this.loading = false;
            this.updateLoadMoreButton(false);
        }
    },

    appendPosts(posts) {
        const container = document.getElementById('recentPosts');
        posts.forEach(post => {
            const postElement = this.createPostElement(post);
            container.appendChild(postElement);
        });
    },

    async refresh() {
        const container = document.getElementById('recentPosts');
        container.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">กำลังโหลด...</span>
                </div>
            </div>
        `;

        try {
            const response = await fetch(`${baseUrl}/share/getPosts?page=1`);
            const data = await response.json();

            if (data.posts) {
                container.innerHTML = '';
                this.appendPosts(data.posts);
                this.page = 1;
                this.hasMore = data.has_more;
            }
        } catch (error) {
            console.error('Error refreshing posts:', error);
            AlertHandler.error('ไม่สามารถรีเฟรชโพสต์ได้');
        }
    },

    updateLoadMoreButton(loading) {
        const btn = document.querySelector('[onclick="RecentPosts.loadMore()"]');
        if (btn) {
            btn.disabled = loading;
            btn.innerHTML = loading ? 
                '<span class="spinner-border spinner-border-sm"></span> กำลังโหลด...' :
                '<i class="fas fa-plus"></i> โหลดเพิ่มเติม';
        }
    }
};

// Initialize infinite scroll
document.addEventListener('DOMContentLoaded', function() {
    const observer = new IntersectionObserver(
        (entries) => {
            if (entries[0].isIntersecting) {
                RecentPosts.loadMore();
            }
        },
        { threshold: 1.0 }
    );

    const loadMoreBtn = document.querySelector('[onclick="RecentPosts.loadMore()"]');
    if (loadMoreBtn) {
        observer.observe(loadMoreBtn);
    }
});
</script>