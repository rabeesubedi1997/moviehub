<div class="slider">
    <div class="slider-container">
        <?php foreach ($sliderMovies as $movie): ?>
            <div class="slider-item">
                <img src="<?php echo $movie['image']; ?>" alt="<?php echo $movie['title']; ?>">
                <div class="slider-caption">
                    <h2><?php echo $movie['title']; ?></h2>
                    <p><?php echo $movie['description']; ?></p>
                    <a href="details.php?id=<?php echo $movie['id']; ?>" class="btn">View Details</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    // JavaScript for slider functionality
    let currentIndex = 0;
    const items = document.querySelectorAll('.slider-item');
    const totalItems = items.length;

    function showSlide(index) {
        items.forEach((item, i) => {
            item.style.display = (i === index) ? 'block' : 'none';
        });
    }

    function nextSlide() {
        currentIndex = (currentIndex + 1) % totalItems;
        showSlide(currentIndex);
    }

    function prevSlide() {
        currentIndex = (currentIndex - 1 + totalItems) % totalItems;
        showSlide(currentIndex);
    }

    showSlide(currentIndex);
    setInterval(nextSlide, 5000); // Change slide every 5 seconds
</script>