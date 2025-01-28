<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('profile/password') ?>" class="text-decoration-none">
                            <i class="fas fa-user me-1"></i>โปรไฟล์
                        </a>
                    </li>
                    <li class="breadcrumb-item active">เปลี่ยนรหัสผ่าน</li>
                </ol>
            </nav>

            <!-- Alert Messages -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Change Password Form -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">เปลี่ยนรหัสผ่าน</h5>
                </div>
                <div class="card-body">
                    <?= form_open('profile/password', ['id' => 'changePasswordForm']) ?>
                    <!-- CSRF Token -->
                    <!-- <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>"> -->
                    <!-- Current Password -->
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">รหัสผ่านเดิม <span class="text-danger">*</span></label>
                        <input type="password"
                            class="form-control <?= (isset($validation) && $validation->hasError('currentPassword')) ? 'is-invalid' : '' ?>"
                            id="currentPassword"
                            name="currentPassword"
                            required>
                        <?php if (isset($validation) && $validation->hasError('currentPassword')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('currentPassword') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- New Password -->
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">รหัสผ่านใหม่ <span class="text-danger">*</span></label>
                        <input type="password"
                            class="form-control <?= (isset($validation) && $validation->hasError('newPassword')) ? 'is-invalid' : '' ?>"
                            id="newPassword"
                            name="newPassword"
                            required>
                        <?php if (isset($validation) && $validation->hasError('newPassword')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('newPassword') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Confirm New Password -->
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">ยืนยันรหัสผ่านใหม่ <span class="text-danger">*</span></label>
                        <input type="password"
                            class="form-control <?= (isset($validation) && $validation->hasError('confirmPassword')) ? 'is-invalid' : '' ?>"
                            id="confirmPassword"
                            name="confirmPassword"
                            required>
                        <?php if (isset($validation) && $validation->hasError('confirmPassword')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('confirmPassword') ?>
                            </div>
                        <?php endif; ?>
                    </div>


                    <!-- Go Back -->
                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('profile') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>กลับ
                        </a>
                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>บันทึกรหัสผ่านใหม่
                        </button>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
    <!-- ใส่ Javascript ที่ด้านล่างของไฟล์ -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('changePasswordForm');

            form.addEventListener('submit', function(event) {
                let valid = true;

                // ตรวจสอบรหัสผ่านเดิม
                const currentPassword = document.getElementById('currentPassword');
                if (currentPassword.value.trim() === '') {
                    valid = false;
                    currentPassword.classList.add('is-invalid');
                } else {
                    currentPassword.classList.remove('is-invalid');
                }

                // ตรวจสอบรหัสผ่านใหม่
                const newPassword = document.getElementById('newPassword');
                if (newPassword.value.trim() === '' || newPassword.value.length < 8) {
                    valid = false;
                    newPassword.classList.add('is-invalid');
                } else {
                    newPassword.classList.remove('is-invalid');
                }

                // ตรวจสอบการยืนยันรหัสผ่านใหม่
                const confirmPassword = document.getElementById('confirmPassword');
                if (confirmPassword.value.trim() === '' || confirmPassword.value !== newPassword.value) {
                    valid = false;
                    confirmPassword.classList.add('is-invalid');
                } else {
                    confirmPassword.classList.remove('is-invalid');
                }

                // หากข้อมูลไม่ถูกต้องให้หยุดการส่งฟอร์ม
                if (!valid) {
                    event.preventDefault();
                }
            });
        });
    </script>

</div>