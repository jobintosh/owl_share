
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 text-center">
            <div class="card-body">
                <div class="mb-3">
                    <i class="fas fa-file-alt fa-3x text-primary"></i>
                </div>
                <h5 class="card-title">จำนวนโพสต์ทั้งหมด</h5>
                <p class="fs-4 fw-bold text-dark" data-count-to="<?= $stats['total_posts'] ?>">0</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 text-center">
            <div class="card-body">
                <div class="mb-3">
                    <i class="fas fa-comments fa-3x text-success"></i>
                </div>
                <h5 class="card-title">จำนวนความคิดเห็น</h5>
                <p class="fs-4 fw-bold text-dark" data-count-to="<?= $stats['total_comments'] ?>">0</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 text-center">
            <div class="card-body">
                <div class="mb-3">
                    <i class="fas fa-users fa-3x text-warning"></i>
                </div>
                <h5 class="card-title">จำนวนสมาชิก</h5>
                <p class="fs-4 fw-bold text-dark" data-count-to="<?= $stats['total_users'] ?>">0</p>
            </div>
        </div>
    </div>
</div>

<script>
     document.addEventListener('DOMContentLoaded', () => {
        const counters = document.querySelectorAll('[data-count-to]');

        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-count-to'); // ตัวเลขเป้าหมาย
                const current = +counter.innerText; // ตัวเลขปัจจุบัน
                const increment = target / 100; // ความเร็วในการเพิ่ม (ยิ่งมากยิ่งเร็ว)

                if (current < target) {
                    counter.innerText = Math.ceil(current + increment); // เพิ่มตัวเลข
                    setTimeout(updateCount, 20); // ความเร็วใน ms
                } else {
                    counter.innerText = target; // แสดงตัวเลขสุดท้าย
                }
            };

            updateCount();
        });
    });
</script>