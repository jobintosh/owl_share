<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'My Website'; ?></title>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* ตั้งค่าตำแหน่งของ popup */
        .swal2-popup {
            position: fixed !important;
            top: 20px; /* ห่างจากขอบบน */
            right: 20px; /* ห่างจากขอบขวา */
            z-index: 9999; /* ให้เด่นกว่าองค์ประกอบอื่น */
        }

        /* รองรับ responsive สำหรับ mobile */
        @media (max-width: 768px) {
            .swal2-popup {
                top: 10px; /* ห่างจากขอบบนมากขึ้นสำหรับมือถือ */
                right: 10px; /* ห่างจากขอบขวา */
            }
        }
    </style>
</head>
<body>
    <?= $this->renderSection('content'); ?>

    <!-- สคริปต์ Popup -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (session()->getFlashdata('success')): ?>
                Swal.fire({
                    title: 'Success!',
                    text: '<?= session()->getFlashdata('success') ?>',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    toast: true, // ใช้โหมด toast
                    position: 'top-right', // ตั้งตำแหน่ง popup ที่มุมขวาบน
                    showConfirmButton: false, // ซ่อนปุ่ม confirm
                    timer: 5000 // ให้ popup หายไปหลังจาก 5 วินาที
                });
            <?php elseif (session()->getFlashdata('error')): ?>
                Swal.fire({
                    title: 'Error!',
                    text: '<?= session()->getFlashdata('error') ?>',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    toast: true, // ใช้โหมด toast
                    position: 'top-right', // ตั้งตำแหน่ง popup ที่มุมขวาบน
                    showConfirmButton: false, // ซ่อนปุ่ม confirm
                    timer: 5000 // ให้ popup หายไปหลังจาก 5 วินาที
                });
            <?php endif; ?>
        });
    </script>
</body>
</html>
