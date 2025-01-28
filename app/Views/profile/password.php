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
                        <label for="current_password" class="form-label">รหัสผ่านเดิม <span class="text-danger">*</span></label>
                        <input type="password"
                            class="form-control <?= (isset($validation) && $validation->hasError('current_password')) ? 'is-invalid' : '' ?>"
                            id="current_password"
                            name="current_password"
                            required>
                        <?php if (isset($validation) && $validation->hasError('current_password')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('current_password') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- New Password -->
                    <div class="mb-3">
                        <label for="new_password" class="form-label">รหัสผ่านใหม่ <span class="text-danger">*</span></label>
                        <input type="password"
                            class="form-control <?= (isset($validation) && $validation->hasError('new_password')) ? 'is-invalid' : '' ?>"
                            id="new_password"
                            name="new_password"
                            required>
                        <?php if (isset($validation) && $validation->hasError('new_password')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('new_password') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Confirm New Password -->
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">ยืนยันรหัสผ่านใหม่ <span class="text-danger">*</span></label>
                        <input type="password"
                            class="form-control <?= (isset($validation) && $validation->hasError('confirm_password')) ? 'is-invalid' : '' ?>"
                            id="confirm_password"
                            name="confirm_password"
                            required>
                        <?php if (isset($validation) && $validation->hasError('confirm_password')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('confirm_password') ?>
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
                const current_password = document.getElementById('current_password');
                if (current_password.value.trim() === '') {
                    valid = false;
                    current_password.classList.add('is-invalid');
                } else {
                    current_password.classList.remove('is-invalid');
                }

                // ตรวจสอบรหัสผ่านใหม่
                const new_password = document.getElementById('new_password');
                if (new_password.value.trim() === '' || new_password.value.length < 8) {
                    valid = false;
                    new_password.classList.add('is-invalid');
                } else {
                    new_password.classList.remove('is-invalid');
                }

                // ตรวจสอบการยืนยันรหัสผ่านใหม่
                const confirm_password = document.getElementById('confirm_password');
                if (confirm_password.value.trim() === '' || confirm_password.value !== new_password.value) {
                    valid = false;
                    confirm_password.classList.add('is-invalid');
                } else {
                    confirm_password.classList.remove('is-invalid');
                }

                // หากข้อมูลไม่ถูกต้องให้หยุดการส่งฟอร์ม
                if (!valid) {
                    event.preventDefault();
                }
            });
        });
    </script>

</div>