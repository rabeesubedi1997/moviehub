<div class="slider">
    <div class="slider-container">
        <?php foreach ($featuredMovies as $index => $movie): ?>
            <div class="slider-item <?php echo $index === 0 ? 'active' : ''; ?>">
                <img src="/MovieHub/movie-website/public/assets/images/movies/<?php echo htmlspecialchars($movie['image']); ?>"
                    alt="<?php echo htmlspecialchars($movie['title']); ?>">
                <div class="slider-caption">
                    <h2 class="text-3xl font-bold mb-2"><?php echo htmlspecialchars($movie['title']); ?></h2>
                    <p class="text-lg mb-4"><?php echo htmlspecialchars(substr($movie['description'], 0, 150)) . '...'; ?></p>
                    <a href="movie_details.php?id=<?php echo $movie['id']; ?>"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full inline-flex items-center">
                        <i class="fas fa-play mr-2"></i> Learn More
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="slider-controls">
        <button class="slider-btn prev-btn">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="slider-btn next-btn">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>

    <div class="slider-indicators">
        <?php foreach ($featuredMovies as $index => $movie): ?>
            <div class="indicator <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>"></div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const items = document.querySelectorAll('.slider-item');
        const indicators = document.querySelectorAll('.indicator');
        const prevBtn = document.querySelector('.prev-btn');
        const nextBtn = document.querySelector('.next-btn');
        let currentIndex = 0;
        let interval;

        function showSlide(index) {
            items.forEach(item => item.classList.remove('active'));
            indicators.forEach(ind => ind.classList.remove('active'));

            items[index].classList.add('active');
            indicators[index].classList.add('active');
        }

        function nextSlide() {
            currentIndex = (currentIndex + 1) % items.length;
            showSlide(currentIndex);
        }

        function prevSlide() {
            currentIndex = (currentIndex - 1 + items.length) % items.length;
            showSlide(currentIndex);
        }

        function startAutoPlay() {
            interval = setInterval(nextSlide, 5000);
        }

        function stopAutoPlay() {
            clearInterval(interval);
        }

        // Event Listeners
        prevBtn.addEventListener('click', () => {
            prevSlide();
            stopAutoPlay();
            startAutoPlay();
        });

        nextBtn.addEventListener('click', () => {
            nextSlide();
            stopAutoPlay();
            startAutoPlay();
        });

        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                currentIndex = index;
                showSlide(currentIndex);
                stopAutoPlay();
                startAutoPlay();
            });
        });

        // Pause on hover
        const slider = document.querySelector('.slider');
        slider.addEventListener('mouseenter', stopAutoPlay);
        slider.addEventListener('mouseleave', startAutoPlay);

        // Start the slider
        startAutoPlay();
    });
</script>