
    
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            font-family: 'Prompt', sans-serif;
        }
        
        .forgot-password-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo-section img {
            width: 70px;
            margin-bottom: 1rem;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
        }
        
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0,0,0,.1);
            padding: 1.5rem;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .breadcrumb {
            background-color: transparent;
            padding: 0.5rem 0;
        }
        
        .breadcrumb-item a {
            color: #0d6efd;
            text-decoration: none;
        }
        
        .breadcrumb-item.active {
            color: #6c757d;
        }
        
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }
        
        .btn-primary {
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
        }
        
        .btn-outline-secondary {
            padding: 0.6rem 1.5rem;
            font-weight: 500;
        }
        
        .alert {
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .invalid-feedback {
            font-size: 0.875rem;
        }
    </style>

    <div class="container">
        <div class="forgot-password-container">
            <!-- <div class="logo-section">
                <img src="/api/placeholder/70/70" alt="Logo" class="mb-2">
                <h4 class="text-primary fw-bold">ShareHub</h4>
            </div>
             -->

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-lock me-2"></i>ขอรีเซ็ตรหัสผ่าน
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        กรุณากรอกอีเมลที่คุณใช้ในการลงทะเบียน เราจะส่งลิงก์สำหรับรีเซ็ตรหัสผ่านไปยังอีเมลของคุณ
                    </p>

                    <?= form_open('/auth/processForgotPassword', ['method' => 'post']) ?>
                        <div class="mb-4">
                            <label for="email" class="form-label">
                                อีเมล <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email"
                                    class="form-control <?= (isset($validation) && $validation->hasError('email')) ? 'is-invalid' : '' ?>"
                                    id="email"
                                    name="email"
                                    placeholder="กรุณากรอกอีเมลของคุณ"
                                    required>
                                <?php if (isset($validation) && $validation->hasError('email')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('email') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="<?= base_url('/auth/login') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>กลับ
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>ส่งลิงก์รีเซ็ตรหัสผ่าน
                            </button>
                        </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>