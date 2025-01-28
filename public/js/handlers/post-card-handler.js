// post-card-handler.js
const PostCardHandler = {
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
                this.updateLikeButton(postId, data.liked, data.likeCount);
            } else {
                AlertHandler.error(data.message);
            }
        } catch (error) {
            console.error('Error toggling like:', error);
            AlertHandler.error('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
        }
    },

    // ... (โค้ดส่วน PostCard ที่เหลือ)
};