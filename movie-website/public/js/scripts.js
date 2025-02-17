document.addEventListener('DOMContentLoaded', function() {
    // Initialize the movie slider
    const slider = document.querySelector('.movie-slider');
    let currentIndex = 0;

    function showSlide(index) {
        const slides = slider.querySelectorAll('.slide');
        slides.forEach((slide, i) => {
            slide.style.display = (i === index) ? 'block' : 'none';
        });
    }

    function nextSlide() {
        const slides = slider.querySelectorAll('.slide');
        currentIndex = (currentIndex + 1) % slides.length;
        showSlide(currentIndex);
    }

    function prevSlide() {
        const slides = slider.querySelectorAll('.slide');
        currentIndex = (currentIndex - 1 + slides.length) % slides.length;
        showSlide(currentIndex);
    }

    // Event listeners for next and previous buttons
    document.querySelector('.next-btn').addEventListener('click', nextSlide);
    document.querySelector('.prev-btn').addEventListener('click', prevSlide);

    // Show the first slide initially
    showSlide(currentIndex);
});