    <style>
        :root {
            --primary-color: #0d6efd;
            --primary-hover: #0a58ca;
            --bg-color: #f8f9fa;
            --card-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }



        .form-container {
            background: white;
            border-radius: 1rem;
            box-shadow: var(--card-shadow);
            padding: 2.5rem;
            max-width: 500px;
            width: 100%;
            margin: 2rem auto;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-section img {
            width: 80px;
            margin-bottom: 1rem;
        }

        .form-floating>.form-control:focus,
        .form-floating>.form-control:not(:placeholder-shown) {
            padding-top: 1.625rem;
            padding-bottom: 0.625rem;
        }

        .form-floating>label {
            padding: 1rem 0.75rem;
        }

        .btn-register {
            padding: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
            color: #6c757d;
            cursor: pointer;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #dee2e6;
        }

        .divider span {
            padding: 0 1rem;
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>


    <div class="container">
        <div class="form-container">
            <div class="logo-section">
                <img src="/images/logo/owl.png" alt="Logo" class="mb-2">
                <h4 class="fw-bold text-primary">สมัครสมาชิก ShareHub</h4>
            </div>

            <?php if (session()->has('errors')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php foreach (session('errors') as $error) : ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif ?>

            <?php if (session()->has('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif ?>

            <form action="<?= base_url('auth/doregister') ?>" method="post">
                <?= csrf_field() ?>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="name" name="name" placeholder="ชื่อ-นามสกุล" value="<?= old('name') ?>" required>
                    <label for="name"><i class="fas fa-user me-2"></i>ชื่อ-นามสกุล</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="อีเมล" value="<?= old('email') ?>" required>
                    <label for="email"><i class="fas fa-envelope me-2"></i>อีเมล</label>
                </div>

                <!-- Add Username Field -->
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="username" name="username" placeholder="ชื่อผู้ใช้" value="<?= old('username') ?>" required>
                    <label for="username"><i class="fas fa-user me-2"></i>ชื่อผู้ใช้</label>
                </div>

                <div class="form-floating mb-3 position-relative">
                    <input type="password" class="form-control" id="password" name="password" placeholder="รหัสผ่าน" required>
                    <label for="password"><i class="fas fa-lock me-2"></i>รหัสผ่าน</label>
                    <button type="button" class="password-toggle" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <div class="form-floating mb-4 position-relative">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="ยืนยันรหัสผ่าน" required>
                    <label for="confirm_password"><i class="fas fa-lock me-2"></i>ยืนยันรหัสผ่าน</label>
                    <button type="button" class="password-toggle" id="toggleConfirmPassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <div class="form-text mb-3">
                    รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร ประกอบด้วยตัวอักษรภาษาอังกฤษ ตัวเลข และอักขระพิเศษ
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-register mb-3">
                    <i class="fas fa-user-plus me-2"></i>สมัครสมาชิก
                </button>

                <div class="divider">
                    <span>หรือ</span>
                </div>

                <div class="text-center">
                    <p class="mb-0">มีบัญชีอยู่แล้ว? <a href="<?= base_url('auth/login') ?>" class="text-primary fw-bold">เข้าสู่ระบบ</a></p>
                </div>
            </form>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = (inputId, buttonId) => {
                const input = document.getElementById(inputId);
                const button = document.getElementById(buttonId);

                button.addEventListener('click', () => {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);

                    const icon = button.querySelector('i');
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                });
            };

            togglePassword('password', 'togglePassword');
            togglePassword('confirm_password', 'toggleConfirmPassword');
        });
    </script>