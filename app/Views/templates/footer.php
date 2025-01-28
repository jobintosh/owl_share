    <style>
        body, html {
            height: 100%;
            margin: 0;
        }

        .footer-container {
            min-height: 100%;
            display: flex;
            flex-direction: column;
        }

        .footer {
            margin-top: auto;
        }

        .footer-logo {
            transition: transform 0.3s ease;
        }

        .footer-logo:hover {
            transform: translateY(-3px);
        }

        .social-icons a {
            transition: opacity 0.3s ease;
        }

        .social-icons a:hover {
            opacity: 0.7;
        }

        .scroll-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            opacity: 0;
            transition: all 0.3s ease;
            padding: 12px 17px;
        }

        .scroll-top.show {
            opacity: 1;
            transform: translateY(-10px);
        }

        .list-unstyled li {
            transition: transform 0.3s ease;
        }

        .list-unstyled li:hover {
            transform: translateX(5px);
        }

        .text-muted {
            color: #6c757d !important;
        }

        .text-primary {
            color: #0d6efd !important;
        }
    </style>

<footer class="border-top bg-body-tertiary p-4 footer">
    <div class="container">
        <div class="row g-4">
            <!-- About Section -->
            <div class="col-md-6 mb-4">
                <div class="footer-logo mb-3 d-flex align-items-center">
                    <i class="fas fa-share-alt fa-2x text-primary me-2"></i>
                    <span class="h4 fw-bold">ShareHub</span>
                </div>
                <p class="text-muted">แพลตฟอร์มแบ่งปันความรู้และประสบการณ์สำหรับทุกคน</p>
                <div class="social-icons mt-4 d-flex">
                    <a href="#" class="text-primary me-3"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-primary me-3"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" class="text-primary"><i class="fab fa-instagram fa-lg"></i></a>
                </div>
            </div>

            <!-- Right Section (Legal + Contact) -->
            <div class="col-md-6 mb-4">
                <div class="row">
                    <!-- Legal Section -->
                    <div class="col-md-6 col-12 mb-4">
                        <h5 class="mb-4 text-primary">กฎหมาย</h5>
                        <ul class="list-unstyled">
                            <li class="mb-3"><a href="#" class="text-decoration-none text-muted">ข้อกำหนดการใช้งาน</a></li>
                            <li class="mb-3"><a href="#" class="text-decoration-none text-muted">นโยบายความเป็นส่วนตัว</a></li>
                            <li><a href="#" class="text-decoration-none text-muted">Cookie Policy</a></li>
                        </ul>
                    </div>

                    <!-- Contact Section -->
                    <div class="col-md-6 col-12 mb-4">
                        <h5 class="mb-4 text-primary">ติดต่อเรา</h5>
                        <ul class="list-unstyled">
                            <li class="mb-3 d-flex align-items-center">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                มหาวิทยาลัยสวนดุสิต
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="fas fa-phone me-2 text-primary"></i>
                                +66 1234 5678
                            </li>
                            <li class="d-flex align-items-center">
                                <i class="fas fa-envelope me-2 text-primary"></i>
                                datacenter@dusit.ac.th
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-top pt-4 mt-4">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="text-muted mb-0">&copy; 2025 ShareHub. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</footer>
