<div class="swiper hero-slider">
    <div class="swiper-wrapper">
        <?php foreach ($sliderData as $slide): ?>
            <div class="swiper-slide">
                <div class="hero-slide" 
                     style="background-image: linear-gradient(rgba(0,0,0,<?= $slide['overlay_opacity'] ?>), 
                                                          rgba(0,0,0,<?= $slide['overlay_opacity'] ?>)), 
                                           url('<?= base_url($slide['image']) ?>');
                            background-position: <?= $slide['background_position'] ?>;">
                    <div class="hero-content">
                        <h2 class="animate__animated animate__fadeInDown">
                            <?= esc($slide['title']) ?>
                        </h2>
                        <p class="animate__animated animate__fadeInUp animate__delay-1s">
                            <?= esc($slide['description']) ?>
                        </p>
                        <?php if (!empty($slide['button_text'])): ?>
                            <a href="<?= esc($slide['button_link']) ?>" 
                               class="btn btn-primary btn-lg animate__animated animate__fadeInUp animate__delay-2s">
                                <?= esc($slide['button_text']) ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="swiper-pagination"></div>
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
</div>

<!-- เพิ่ม CSS เฉพาะสำหรับ Hero Slider -->
<style>
.hero-slider {
    height: 600px;
}

.hero-slide {
    width: 100%;
    height: 100%;
    background-size: cover;
    background-repeat: no-repeat;
}

.hero-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    color: white;
    width: 80%;
    max-width: 800px;
}

.hero-content h2 {
    font-size: 3rem;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}

.hero-content p {
    font-size: 1.5rem;
    margin-bottom: 2rem;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

/* Custom swiper buttons */
.swiper-button-next,
.swiper-button-prev {
    color: white;
    text-shadow: 0 0 3px rgba(0,0,0,0.5);
}

.swiper-pagination-bullet {
    background: white;
    opacity: 0.7;
}

.swiper-pagination-bullet-active {
    background: #0d6efd;
    opacity: 1;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .hero-slider,
    .hero-slide {
        height: 450px;
    }

    .hero-content h2 {
        font-size: 2.5rem;
    }

    .hero-content p {
        font-size: 1.2rem;
    }
}

@media (max-width: 768px) {
    .hero-slider,
    .hero-slide {
        height: 350px;
    }

    .hero-content h2 {
        font-size: 2rem;
    }

    .hero-content p {
        font-size: 1rem;
    }
}

/* Animation classes */
.animate__animated {
    animation-duration: 1s;
    animation-fill-mode: both;
}

.animate__delay-1s {
    animation-delay: 0.5s;
}

.animate__delay-2s {
    animation-delay: 1s;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translate3d(0, -100%, 0);
    }
    to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translate3d(0, 100%, 0);
    }
    to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
}

.animate__fadeInDown {
    animation-name: fadeInDown;
}

.animate__fadeInUp {
    animation-name: fadeInUp;
}
</style>

<!-- Swiper initialization -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    new Swiper('.hero-slider', {
        slidesPerView: 1,
        spaceBetween: 0,
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
        on: {
            slideChange: function() {
                // Reset and play animations
                const activeSlide = this.slides[this.activeIndex];
                const elements = activeSlide.querySelectorAll('.animate__animated');
                elements.forEach(element => {
                    element.style.opacity = 0;
                    void element.offsetWidth;
                    element.style.opacity = 1;
                });
            }
        }
    });
});
</script>