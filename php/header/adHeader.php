<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .modal {
            display: none;
            /* Hide modal by default */
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease-out;
        }

        .modal.active {
            display: flex;
            /* Show modal when active */
        }

        .modal-content {
            position: relative;
            width: 90%;
            max-width: 800px;
            animation: slideUp 0.3s ease-out;
        }

        .close-btn {
            position: absolute;
            right: -40px;
            top: -40px;
            width: 32px;
            height: 32px;
            background-color: white;
            border: none;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s;
        }

        .close-btn:hover {
            background-color: #f3f4f6;
        }

        .carousel-container {
            position: relative;
            width: 100%;
            height: 400px;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .carousel-track {
            display: flex;
            height: 100%;
            transition: transform 0.5s ease-out;
            will-change: transform; /* Optimize for transform changes */
        }

        .carousel-slide {
            flex: 0 0 100%;
            height: 100%;
        }

        .carousel-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .carousel-control {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.8);
            border: none;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
            opacity: 0.8;
            transition: all 0.2s;
        }

        .carousel-control:hover {
            opacity: 1;
            background-color: white;
        }

        .carousel-control.prev {
            left: 16px;
        }

        .carousel-control.next {
            right: 16px;
        }

        .carousel-indicators {
            position: absolute;
            bottom: 16px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
        }

        .indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
            transition: all 0.2s;
        }

        .indicator.active {
            background-color: white;
            transform: scale(1.25);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .carousel-container {
                height: 300px;
            }

            .carousel-control {
                width: 36px;
                height: 36px;
            }

            .close-btn {
                right: 8px;
                top: 8px;
            }
        }

        @media (max-width: 480px) {
            .carousel-container {
                height: 240px;
            }

            .carousel-control {
                width: 32px;
                height: 32px;
            }
        }
    </style>
</head>

<body>
    <div class="modal active" id="carouselModal">
        <div class="modal-content">
            <button class="close-btn">&times;</button>

            <div class="carousel-container">
                <div class="carousel-track">
                    <div class="carousel-slide">
                        <a href="https://abaro.com.my/" target="_blank">
                            <img src="..\..\image\ads\ad1.webp">
                        </a>
                    </div>
                    <div class="carousel-slide">
                        <a href="https://www.apple.com/my-edu/store" target="_blank">
                            <img src="..\..\image\ads\ad2.webp">
                        </a>
                    </div>
                    <div class="carousel-slide">
                        <a href="https://mphonline.com/" target="_blank">
                            <img src="..\..\image\ads\ad3.webp">
                        </a>
                    </div>
                </div>

                <button class="carousel-control prev">&lt;</button>
                <button class="carousel-control next">&gt;</button>

                <div class="carousel-indicators"></div>
            </div>
        </div>
    </div>
    </div>
    <script>
        class ImageCarousel {
            constructor(container, autoSlideInterval = 4000) {
                this.container = container;
                this.track = container.querySelector('.carousel-track');
                this.slides = Array.from(container.querySelectorAll('.carousel-slide'));
                this.indicators = container.querySelector('.carousel-indicators');
                this.prevBtn = container.querySelector('.prev');
                this.nextBtn = container.querySelector('.next');

                this.currentIndex = 0;
                this.autoSlideInterval = autoSlideInterval;
                this.autoSlideTimer = null;
                this.isTransitioning = false;

                // Preload images first
                this.preloadImages();

                // Then set up indicators, event listeners, and auto-slide
                this.setupIndicators();
                this.setupEventListeners();
                this.startAutoSlide();
            }

            preloadImages() {
                this.slides.forEach(slide => {
                    const img = slide.querySelector('img');
                    if (img.complete) return; // Skip if already loaded
                    const tempImg = new Image();
                    tempImg.src = img.src;
                });
            }

            setupIndicators() {
                this.slides.forEach((_, index) => {
                    const indicator = document.createElement('div');
                    indicator.className = `indicator ${index === 0 ? 'active' : ''}`;
                    indicator.addEventListener('click', () => this.goToSlide(index));
                    this.indicators.appendChild(indicator);
                });
            }

            setupEventListeners() {
                this.prevBtn.addEventListener('click', () => this.goToPrevSlide());
                this.nextBtn.addEventListener('click', () => this.goToNextSlide());
                this.track.addEventListener('transitionend', () => {
                    this.isTransitioning = false;
                    this.startAutoSlide();
                });
            }

            updateIndicators() {
                const indicators = this.indicators.querySelectorAll('.indicator');
                indicators.forEach((indicator, index) => {
                    indicator.classList.toggle('active', index === this.currentIndex);
                });
            }

            goToSlide(index) {
                if (this.isTransitioning || index === this.currentIndex) return;

                this.isTransitioning = true;
                this.currentIndex = index;
                this.track.style.transform = `translate3d(-${index * 100}%, 0, 0)`; // Use translate3d
                this.updateIndicators();
                this.stopAutoSlide();
            }

            goToNextSlide() {
                const nextIndex = (this.currentIndex + 1) % this.slides.length;
                this.goToSlide(nextIndex);
            }

            goToPrevSlide() {
                const prevIndex = (this.currentIndex - 1 + this.slides.length) % this.slides.length;
                this.goToSlide(prevIndex);
            }

            startAutoSlide() {
                this.stopAutoSlide();
                this.autoSlideTimer = setInterval(() => this.goToNextSlide(), this.autoSlideInterval);
            }

            stopAutoSlide() {
                if (this.autoSlideTimer) {
                    clearInterval(this.autoSlideTimer);
                    this.autoSlideTimer = null;
                }
            }
        }

        const modal = document.getElementById('carouselModal');
        const closeBtn = document.querySelector('.close-btn');
        const carouselContainer = document.querySelector('.carousel-container');

        closeBtn.addEventListener('click', () => {
            modal.classList.remove('active');
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });

        const carousel = new ImageCarousel(carouselContainer);
    </script>
</body>

</html>