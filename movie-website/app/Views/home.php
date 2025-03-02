<?php require_once __DIR__ . '/layouts/header.php'; ?>

<!-- Hero Section with Video Background -->
<div class="relative min-h-[500px] md:h-screen overflow-hidden">
    <div class="absolute inset-0">
        <iframe class="w-full h-full"
            src="https://www.youtube.com/embed/tGpTpVyI_OQ?autoplay=1&mute=1&loop=1&playlist=tGpTpVyI_OQ&controls=0&showinfo=0&rel=0&modestbranding=1"
            title="Background Video" frameborder="0" allowfullscreen>
        </iframe>
    </div>
    <div class="absolute inset-0 bg-black bg-opacity-60"></div>
    <div class="relative container mx-auto px-4 h-full flex items-center">
        <div class="text-white max-w-3xl mx-auto text-center md:text-left">
            <h1 class="text-4xl md:text-6xl font-bold mb-4 md:mb-6">Welcome to MovieHub</h1>
            <p class="text-xl md:text-2xl mb-6 md:mb-8">Discover the magic of cinema</p>
            <a href="#featured-movies" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 md:py-3 md:px-8 rounded-full">
                <i class="fas fa-play mr-2"></i>
                Explore Movies
            </a>
        </div>
    </div>
</div>

<!-- Featured Movies Section -->
<section id="featured-movies" class="py-12 bg-gray-900">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-white mb-8">Featured Movies</h2>
        <div class="swiper-container featured-slider">
            <div class="swiper-wrapper">
                <?php foreach ($featuredMovies as $movie): ?>
                    <div class="swiper-slide">
                        <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg mx-2">
                            <img src="/MovieHub/movie-website/public/assets/images/movies/<?php echo htmlspecialchars($movie['image']); ?>"
                                alt="<?php echo htmlspecialchars($movie['title']); ?>"
                                class="w-full h-64 object-cover">
                            <div class="p-4">
                                <h3 class="text-xl font-bold text-white"><?php echo htmlspecialchars($movie['title']); ?></h3>
                                <p class="text-gray-400 mt-2"><?php echo htmlspecialchars($movie['genre']); ?></p>
                                <a href="/MovieHub/movie-website/public/movie/<?php echo $movie['id']; ?>"
                                    class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Watch Now
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>
</section>

<!-- Latest Movies Section -->
<section class="py-12 bg-gray-900">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-white mb-8">Latest Movies</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($latestMovies as $movie): ?>
                <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg transform transition duration-300 hover:scale-105">
                    <img src="/MovieHub/movie-website/public/assets/images/movies/<?php echo htmlspecialchars($movie['image']); ?>"
                        alt="<?php echo htmlspecialchars($movie['title']); ?>"
                        class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="text-xl font-bold text-white"><?php echo htmlspecialchars($movie['title']); ?></h3>
                        <p class="text-gray-400 mt-2"><?php echo htmlspecialchars($movie['genre']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
</section>

<!-- News Section -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Latest News</h2>
            <a href="/MovieHub/movie-website/public/news" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-full transition duration-300">
                View All News
            </a>
        </div>

        <!-- Latest News Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php 
            $latestNews = $newsController->getLatestNews(3); // Get exactly 3 news items
            if (!empty($latestNews)):
                foreach ($latestNews as $news): 
            ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300">
                    <?php if ($news['image']): ?>
                        <img src="/MovieHub/movie-website/public/assets/images/news/<?php echo htmlspecialchars($news['image']); ?>"
                             alt="<?php echo htmlspecialchars($news['title']); ?>"
                             class="w-full h-48 object-cover">
                    <?php endif; ?>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">
                            <a href="/MovieHub/movie-website/public/news/article?id=<?php echo $news['id']; ?>"
                               class="text-gray-900 hover:text-blue-600 transition duration-300">
                                <?php echo htmlspecialchars($news['title']); ?>
                            </a>
                        </h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                            <?php echo substr(strip_tags($news['content']), 0, 100) . '...'; ?>
                        </p>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span><?php echo date('M d, Y', strtotime($news['created_at'])); ?></span>
                            <a href="/MovieHub/movie-website/public/news/article?id=<?php echo $news['id']; ?>"
                               class="text-blue-600 hover:text-blue-800 transition duration-300">
                                Read More →
                            </a>
                        </div>
                    </div>
                </div>
            <?php 
                endforeach; 
            else: 
            ?>
                <div class="col-span-3 text-center py-12">
                    <p class="text-gray-600">No news articles available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Initialize Swiper -->
<script>
    var featuredSwiper = new Swiper('.featured-slider', {
        slidesPerView: 1,
        spaceBetween: 30,
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
        breakpoints: {
            640: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
            }
        }
    });

    // Initialize News Slider
    var newsSwiper = new Swiper('.news-slider', {
        slidesPerView: 1,
        spaceBetween: 30,
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
        }
    });
</script>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>