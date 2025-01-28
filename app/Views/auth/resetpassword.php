<div class="container mt-5">
        <h2>รีเซ็ตรหัสผ่าน</h2>
        <form method="post" action="<?= base_url('/auth/processResetPassword') ?>">
            <div class="mb-3">
                <label for="new_password" class="form-label">รหัสผ่านใหม่</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <input type="hidden" name="token" value="<?= $token; ?>">
            <button type="submit" class="btn btn-primary">รีเซ็ตรหัสผ่าน</button>
        </form>
    </div>