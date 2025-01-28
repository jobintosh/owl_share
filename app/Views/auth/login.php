<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 login-card">
                <div class="card-body p-5">
                    <!-- Header Section -->
                    <div class="text-center mb-5">
                        <h2 class="mb-3">ยินดีต้อนรับกลับ</h2>
                        <p class="text-muted">เข้าสู่ระบบบัญชีของคุณ</p>
                    </div>

                    <!-- Social Login Buttons -->
                    <div class="mb-4">
                        <a href="<?= $facebook_url ?>" class="btn btn-primary w-100 mb-2 d-flex align-items-center justify-content-center">
                            <i class="fab fa-facebook-f me-2"></i> เข้าสู่ระบบด้วย Facebook
                        </a>
                        <a href="<?= $google_url ?>" class="btn btn-danger w-100 d-flex align-items-center justify-content-center">
                            <i class="fab fa-google me-2"></i> เข้าสู่ระบบด้วย Google
                        </a>
                    </div>

                    <div class="text-center position-relative my-4">
                        <span class="px-3 bg-white text-muted">หรือ</span>
                        <div class="border-top position-absolute w-100" style="top: 50%; transform: translateY(-50%);"></div>
                    </div>

                    <!-- Login Form -->
                    <?= form_open('../auth/dologin', ['class' => 'mt-4']) ?>
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">อีเมล</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="กรอกอีเมล" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">รหัสผ่าน</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="กรอกรหัสผ่าน" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">เข้าสู่ระบบ</button>
                    <?= form_close() ?>

                    <!-- Footer Links -->
                    <div class="text-center mt-4">
                        <p class="text-muted small mb-1">ลืมรหัสผ่าน? <a href="<?= base_url('auth/forgot') ?>" class="text-decoration-none">กดที่นี่</a></p>
                        <p class="text-muted small">ยังไม่มีบัญชี? <a href="<?= base_url('auth/register') ?>" class="text-decoration-none">สมัครสมาชิก</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.login-card {
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(8px);
}

.login-card .form-control {
    border-radius: 8px;
}

.login-card .btn {
    border-radius: 8px;
    padding: 12px;
}

.login-card .btn-facebook {
    background-color: #3b5998;
    border-color: #3b5998;
    color: white;
}

.login-card .btn-google {
    background-color: #dd4b39;
    border-color: #dd4b39;
    color: white;
}

.login-card .text-muted {
    font-size: 0.9rem;
}
</style>

<script>
// Add loading animation for social buttons
const socialButtons = document.querySelectorAll('.login-card .btn-facebook, .login-card .btn-google');
socialButtons.forEach(button => {
    button.addEventListener('click', function () {
        this.classList.add('disabled');
        this.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>${this.textContent}`;
    });
});

// Prevent form resubmission
const loginForm = document.querySelector('.login-card form');
loginForm.addEventListener('submit', function (e) {
    e.preventDefault();
    this.querySelector('button[type=submit]').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังเข้าสู่ระบบ';
    this.querySelector('button[type=submit]').disabled = true;
    // Replace the following line with form submission logic
    setTimeout(() => this.submit(), 1000);
});
</script>
