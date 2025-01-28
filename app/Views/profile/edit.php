<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('profile/update') ?>" class="text-decoration-none">
                            <i class="fas fa-user me-1"></i>โปรไฟล์
                        </a>
                    </li>
                    <li class="breadcrumb-item active">แก้ไขโปรไฟล์</li>
                </ol>
            </nav>

            <!-- Alert Messages -->
            <!-- <?php if (session()->getFlashdata('error')): ?>
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
            <?php endif; ?> -->

            <!-- Edit Profile Form -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">แก้ไขโปรไฟล์</h5>
                </div>
                <!-- แก้ตรงนี้ -->
                <div class="card-body">

                    <?= form_open_multipart('profile/update', ['id' => 'editProfileForm']) ?>
                    <!-- ใส่ CSRF Token -->
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

                    <!-- ส่วนอื่นของฟอร์มเหมือนเดิม -->
                    <!-- Profile Picture -->
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <?php if ($user['avatar']): ?>
                                <img src="<?= base_url($user['avatar']) ?>"
                                    class="rounded-circle img-thumbnail"
                                    style="width: 150px; height: 150px; object-fit: cover;"
                                    alt="<?= esc($user['name']) ?>"
                                    id="avatarPreview">
                            <?php else: ?>
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto"
                                    style="width: 150px; height: 150px; font-size: 3rem;"
                                    id="avatarPlaceholder">
                                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            <!-- Upload Button -->
                            <label for="avatarInput" class="position-absolute bottom-0 end-0 mb-2 me-2">
                                <div class="btn btn-primary btn-sm rounded-circle">
                                    <i class="fas fa-camera"></i>
                                </div>
                            </label>
                            <input type="file"
                                id="avatarInput"
                                name="avatar"
                                class="d-none"
                                accept="image/*">
                        </div>
                        <?php if (isset($validation) && $validation->hasError('avatar')): ?>
                            <div class="text-danger mt-2">
                                <?= $validation->getError('avatar') ?>
                            </div>
                        <?php endif; ?>
                        <div class="text-muted small mt-2">
                            อัพโหลดรูปโปรไฟล์ (ไฟล์ JPG, PNG ขนาดไม่เกิน 1MB)
                        </div>
                    </div>

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control <?= (isset($validation) && $validation->hasError('name')) ? 'is-invalid' : '' ?>"
                            id="name"
                            name="name"
                            value="<?= old('name', $user['name']) ?>"
                            required>
                        <?php if (isset($validation) && $validation->hasError('name')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('name') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Bio -->
                    <div class="mb-3">
                        <label for="bio" class="form-label">เกี่ยวกับฉัน</label>
                        <textarea class="form-control <?= (isset($validation) && $validation->hasError('bio')) ? 'is-invalid' : '' ?>"
                            id="bio"
                            name="bio"
                            rows="4"><?= old('bio', $user['bio']) ?></textarea>
                        <?php if (isset($validation) && $validation->hasError('bio')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('bio') ?>
                            </div>
                        <?php endif; ?>
                        <div class="text-muted small">
                            เขียนข้อความแนะนำตัวสั้นๆ (ไม่เกิน 500 ตัวอักษร)
                        </div>
                    </div>

                    <!-- Go Back -->
                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('profile') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>กลับ
                        </a>
                        <!-- Submit Buttons --> 
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>บันทึกการเปลี่ยนแปลง
                        </button>
                    </div>
                    <?= form_close() ?>

                    <!-- จบบบบบบ -->

                    <hr class="my-4">

                    <!-- Delete Account -->
                    <div class="text-center">
                        <h5 class="text-danger mb-3">ลบบัญชีผู้ใช้</h5>
                        <p class="text-muted mb-3">
                            เมื่อคุณลบบัญชีผู้ใช้ ข้อมูลทั้งหมดของคุณจะถูกลบออกจากระบบและไม่สามารถกู้คืนได้
                        </p>
                        <button type="button"
                            class="btn btn-outline-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#deleteAccountModal">
                            <i class="fas fa-trash-alt me-2"></i>ลบบัญชีผู้ใช้
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>ยืนยันการลบบัญชี
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <strong>คำเตือน!</strong> การลบบัญชีจะ:
                    <ul class="mb-0">
                        <li>ลบข้อมูลส่วนตัวทั้งหมด</li>
                        <li>ลบโพสต์และความคิดเห็นทั้งหมด</li>
                        <li>ยกเลิกการเชื่อมต่อโซเชียลมีเดีย</li>
                        <li>ไม่สามารถกู้คืนข้อมูลได้</li>
                    </ul>
                </div>
                <p>กรุณาพิมพ์ <strong>DELETE</strong> เพื่อยืนยันการลบบัญชี:</p>
                <input type="text"
                    class="form-control"
                    id="deleteConfirm"
                    placeholder="พิมพ์ DELETE">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    ยกเลิก
                </button>
                <?= form_open('profile/delete', ['id' => 'deleteAccountForm']) ?>
                <button type="submit"
                    class="btn btn-danger"
                    id="deleteAccountBtn"
                    disabled>
                    ยืนยันการลบบัญชี
                </button>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    // Preview Avatar Image
    document.getElementById('avatarInput')?.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];

            // ตรวจสอบขนาดไฟล์ (1MB = 1048576 bytes)
            if (file.size > 1048576) {
                alert('ขนาดไฟล์ต้องไม่เกิน 1MB');
                e.target.value = '';
                return;
            }

            // ตรวจสอบประเภทไฟล์
            if (!file.type.startsWith('image/')) {
                alert('กรุณาเลือกไฟล์รูปภาพเท่านั้น');
                e.target.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('avatarPreview');
                const placeholder = document.getElementById('avatarPlaceholder');

                if (preview) {
                    preview.src = e.target.result;
                } else if (placeholder) {
                    // สร้าง element ใหม่แทน placeholder
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.id = 'avatarPreview';
                    img.className = 'rounded-circle img-thumbnail';
                    img.style.width = '150px';
                    img.style.height = '150px';
                    img.style.objectFit = 'cover';
                    placeholder.replaceWith(img);
                }
            }
            reader.readAsDataURL(file);
        }
    });

    // Delete Account Confirmation
    document.getElementById('deleteConfirm')?.addEventListener('input', function(e) {
        document.getElementById('deleteAccountBtn').disabled = e.target.value !== 'DELETE';
    });

    // Form Validation
    document.getElementById('editProfileForm')?.addEventListener('submit', function(e) {
        const nameInput = document.getElementById('name');
        const bioInput = document.getElementById('bio');

        // ตรวจสอบ Name
        if (!nameInput.value.trim()) {
            e.preventDefault(); // หยุดการส่งฟอร์ม
            nameInput.classList.add('is-invalid'); // เพิ่มคลาสเพื่อแสดงข้อผิดพลาด
            console.log('Error: Name is required but empty.'); // Log ข้อผิดพลาด
            return;
        } else {
            nameInput.classList.remove('is-invalid'); // ลบคลาสถ้าไม่มีปัญหา
        }

        // ตรวจสอบความยาว Bio
        if (bioInput.value.length > 500) {
            e.preventDefault(); // หยุดการส่งฟอร์ม
            bioInput.classList.add('is-invalid'); // เพิ่มคลาสเพื่อแสดงข้อผิดพลาด
            console.log(`Error: Bio exceeds 500 characters. Current length: ${bioInput.value.length}`); // Log ข้อผิดพลาด
            return;
        } else {
            bioInput.classList.remove('is-invalid'); // ลบคลาสถ้าไม่มีปัญหา
        }

        console.log('Form submission is valid.'); // Log เมื่อข้อมูลถูกต้อง
    });


    // Auto-hide Alerts
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    });
</script>