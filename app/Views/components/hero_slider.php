<div class="swiper hero-slider">
    <div class="swiper-wrapper">
        <?php foreach ($sliderData as $slide): ?>
            <div class="swiper-slide">
                <div class="hero-slide" 
                     data-background="<?= base_url($slide['image']) ?>" 
                     style="background-image: linear-gradient(rgba(0,0,0,<?= $slide['overlay_opacity'] ?>), 
                            rgba(0,0,0,<?= $slide['overlay_opacity'] ?>)), 
                            url('<?= base_url($slide['image']) ?>');
                            background-position: <?= $slide['background_position'] ?? 'center' ?>;">
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

<!-- Preload images -->
<div style="display: none;">
    <?php foreach ($sliderData as $slide): ?>
        <img src="<?= base_url($slide['image']) ?>" alt="preload">
    <?php endforeach; ?>
</div>

<style>
/* Hero Slider Styles */
.hero-slider {
    height: 600px;
    position: relative;
    overflow: hidden;
}

.hero-slide {
    width: 100%;
    height: 100%;
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center center;
    transition: opacity 0.3s ease;
    will-change: transform;
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
}

.hero-slide::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.3);
    z-index: 1;
}

.hero-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) translateZ(0);
    text-align: center;
    color: white;
    width: 80%;
    max-width: 800px;
    z-index: 2;
    -webkit-font-smoothing: antialiased;
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const swiper = new Swiper('.hero-slider', {
        // Basic settings
        slidesPerView: 1,
        spaceBetween: 0,
        loop: true,
        speed: 1000,
        watchSlidesProgress: true,
        observer: true,
        observeParents: true,
        
        // Autoplay configuration
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
        
        // Pagination
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
            dynamicBullets: true,
        },
        
        // Navigation arrows
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        
        // Transition effect
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
        
        // Event handlers
        on: {
            init: function() {
                this.el.addEventListener('mouseenter', () => {
                    this.autoplay.stop();
                });
                this.el.addEventListener('mouseleave', () => {
                    this.autoplay.start();
                });
            },
            beforeTransitionStart: function() {
                const nextSlide = this.slides[this.activeIndex];
                if (nextSlide) {
                    const bgImage = nextSlide.querySelector('.hero-slide');
                    if (bgImage) {
                        bgImage.style.visibility = 'visible';
                        bgImage.style.opacity = '1';
                    }
                }
            },
            slideChange: function() {
                const activeSlide = this.slides[this.activeIndex];
                if (activeSlide) {
                    const elements = activeSlide.querySelectorAll('.animate__animated');
                    elements.forEach(element => {
                        element.style.opacity = '0';
                        void element.offsetWidth;
                        element.style.opacity = '1';
                    });
                }
            }
        }
    });
    // docker-env/webhost-1/app/owl_share/app/Views/components/hero_slider.php
    // Error handling for images
    window.addEventListener('error', function(e) {
        if (e.target.tagName === 'IMG') {
            console.error('Image loading error:', e);
            e.target.src = 'images/fallback/fallback.png'; //fallback fix error loop no path loading
        }
    }, true);
});
</script>