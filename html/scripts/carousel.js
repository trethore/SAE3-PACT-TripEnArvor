document.addEventListener("DOMContentLoaded", function() {

    const carousels = document.getElementsByClassName("carousel");

    for (const carousel of carousels) {
        
        const carouselSlidesContainer = carousel.querySelector(".carousel-slides");
        const carouselSlides = carousel.getElementsByClassName("slide");
        const nbSlides = carouselSlides.length;
        const prevSlideButton = carousel.querySelector(".prev-slide");
        const nextSlideButton = carousel.querySelector(".next-slide");

        let carouselPos = 0;
        let timeoutId;
        const timeoutDuration = 10000;

        function updateCarousel() {
            carouselSlidesContainer.style.transform = `translate(-${carouselPos}00%, 0)`;
        }

        function prevSlide() {
            carouselPos--;
            if (carouselPos < 0) {
                carouselPos = nbSlides - 1;
            }
            updateCarousel();
            clearTimeout(timeoutId);
            timeoutId = setTimeout(nextSlide, timeoutDuration);
        }

        function nextSlide() {
            carouselPos++;
            if (carouselPos > nbSlides - 1) {
                carouselPos = 0;
            }
            updateCarousel();
            clearTimeout(timeoutId);
            timeoutId = setTimeout(nextSlide, timeoutDuration);
        }

        prevSlideButton.addEventListener("click", prevSlide);
        nextSlideButton.addEventListener("click", nextSlide);
        timeoutId = setTimeout(nextSlide, timeoutDuration);
    }
});

