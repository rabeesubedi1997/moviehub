<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Fetch latest movies
try {
    $stmt = $pdo->query("SELECT * FROM movies ORDER BY created_at DESC LIMIT 6");
    $latestMovies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch featured movies
    $featuredStmt = $pdo->query("SELECT * FROM movies WHERE is_featured = 1 LIMIT 3");
    $featuredMovies = $featuredStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $latestMovies = [];
    $featuredMovies = [];
}

require_once __DIR__ . '/../app/Views/layouts/header.php';
?>

<!-- Hero Section with Video Background -->
<div class="relative h-screen overflow-hidden">
    <div class="absolute inset-0">
        <iframe
            class="w-full h-full"
            src="https://www.youtube.com/embed/tGpTpVyI_OQ?autoplay=1&mute=1&loop=1&playlist=tGpTpVyI_OQ&controls=0&showinfo=0&rel=0&modestbranding=1"
            title="Background Video"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen>
        </iframe>
    </div>
    <div class="absolute inset-0 bg-black bg-opacity-60"></div>
    <div class="relative container mx-auto px-4 h-full flex items-center">
        <div class="text-white max-w-3xl">
            <h1 class="text-6xl font-bold mb-6">Welcome to MovieHub</h1>
            <p class="text-2xl mb-8">Discover the magic of cinema</p>
            <a href="https://www.youtube.com/watch?v=tGpTpVyI_OQ" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full inline-flex items-center">
                <i class="fas fa-play mr-2"></i>
                Explore Movies
            </a>
        </div>
    </div>
</div>

<!-- Featured Movies Section -->
<section class="py-20 bg-gray-900">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-white mb-12">Featured Films</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($featuredMovies as $movie): ?>
                <div class="group relative overflow-hidden rounded-lg transform transition-all duration-300 hover:scale-105">
                    <img src="/MovieHub/movie-website/public/assets/images/movies/<?php echo htmlspecialchars($movie['image']); ?>"
                        alt="<?php echo htmlspecialchars($movie['title']); ?>"
                        class="w-full h-96 object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="absolute bottom-0 left-0 right-0 p-6">
                            <h3 class="text-2xl font-bold text-white mb-2"><?php echo htmlspecialchars($movie['title']); ?></h3>
                            <p class="text-gray-300 mb-4"><?php echo htmlspecialchars($movie['genre']); ?></p>
                            <a href="#" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700">
                                Watch Now
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-4xl font-bold mb-6">About MovieHub</h2>
                <p class="text-gray-600 mb-8">Welcome to MovieHub, where we bring you the best in entertainment. Our commitment to quality cinema spans decades, delivering unforgettable stories to audiences worldwide.</p>
                <a href="#" class="bg-blue-600 text-white px-6 py-3 rounded-full hover:bg-blue-700">Learn More</a>
            </div>
            <div class="relative">
                <img src="/MovieHub/movie-website/public/assets/images/about-image.jpg" alt="About MovieHub" class="rounded-lg shadow-xl">
            </div>
        </div>
    </div>
</section>

<!-- News Section -->
<section id="news" class="py-20 bg-gray-100">
    <div class="container mx-auto px-4">
        <h2 class="text-4xl font-bold mb-12 text-center">Latest News</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- News Items -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <img src="/MovieHub/movie-website/public/assets/images/news/news1.jpg" alt="News 1" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Upcoming Release</h3>
                    <p class="text-gray-600 mb-4">Get ready for our next blockbuster...</p>
                    <a href="#" class="text-blue-600 hover:text-blue-800">Read More →</a>
                </div>
            </div>
            <!-- Add more news items -->
        </div>
    </div>
</section>

<!-- Latest Movies Grid -->
<section class="py-20 bg-gray-100">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold mb-12">Latest Releases</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8">
            <?php foreach ($latestMovies as $movie): ?>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <img src="/MovieHub/movie-website/public/assets/images/movies/<?php echo htmlspecialchars($movie['image']); ?>"
                        alt="<?php echo htmlspecialchars($movie['title']); ?>"
                        class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">
                            <?php echo htmlspecialchars($movie['title']); ?>
                        </h3>
                        <p class="text-gray-600 mb-4">
                            <?php echo htmlspecialchars($movie['genre']); ?>
                        </p>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">
                                Released: <?php echo date('M d, Y', strtotime($movie['release_date'])); ?>
                            </span>
                            <a href="#" class="text-blue-600 hover:text-blue-800">
                                Learn More
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-20 bg-gray-900 text-white">
    <div class="container mx-auto px-4">
        <h2 class="text-4xl font-bold mb-12 text-center">Get in Touch</h2>
        <div class="max-w-3xl mx-auto">
            <form class="space-y-6">
                <div>
                    <label class="block text-sm font-medium mb-2">Name</label>
                    <input type="text" class="w-full px-4 py-2 rounded-lg bg-gray-800 border border-gray-700 text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Email</label>
                    <input type="email" class="w-full px-4 py-2 rounded-lg bg-gray-800 border border-gray-700 text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Message</label>
                    <textarea rows="4" class="w-full px-4 py-2 rounded-lg bg-gray-800 border border-gray-700 text-white"></textarea>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-full hover:bg-blue-700">Send Message</button>
            </form>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../app/Views/layouts/footer.php'; ?>