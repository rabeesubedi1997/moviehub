<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Controllers/MovieController.php';
require_once __DIR__ . '/../app/Controllers/NewsController.php';

$movieController = new MovieController($pdo);
$newsController = new NewsController($pdo);

// Simple router
$action = $_GET['action'] ?? '';

if ($action === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $movieController->store($_POST, $_FILES['image']);
        $_SESSION['success'] = "Movie added successfully!";
        header('Location: /MovieHub/movie-website/app/Views/admin/dashboard.php');
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: /MovieHub/movie-website/app/Views/admin/add_movie.php');
    }
    exit();
}

if ($action === 'store_news' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $file = isset($_FILES['image']) ? $_FILES['image'] : null;
        $newsController->store($_POST, $file);
        $_SESSION['success'] = "News article added successfully!";
        header('Location: /MovieHub/movie-website/app/Views/admin/dashboard.php');
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: /MovieHub/movie-website/app/Views/admin/add_news.php');
    }
    exit();
}

if ($action === 'update_news' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $newsController->update($_GET['id'], $_POST, $_FILES['image'] ?? null);
        $_SESSION['success'] = "News article updated successfully!";
        header('Location: /MovieHub/movie-website/app/Views/admin/manage_news.php');
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: /MovieHub/movie-website/app/Views/admin/edit_news.php?id=' . $_GET['id']);
    }
    exit();
}

if ($action === 'delete_news' && $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    try {
        $newsController->delete($_GET['id']);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit();
}

// Add these routes to your existing router
if ($action === 'news_list') {
    try {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 9;
        $offset = ($page - 1) * $limit;

        $newsStmt = $pdo->prepare("
            SELECT * FROM news 
            WHERE status = 'public' 
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $newsStmt->bindValue(1, $limit, PDO::PARAM_INT);
        $newsStmt->bindValue(2, $offset, PDO::PARAM_INT);
        $newsStmt->execute();

        $news = $newsStmt->fetchAll(PDO::FETCH_ASSOC);
        require_once __DIR__ . '/../app/Views/news/index.php';
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: /MovieHub/movie-website/public/index.php');
    }
    exit();
}

if ($action === 'news_detail') {
    try {
        $newsId = $_GET['id'] ?? null;
        if (!$newsId) {
            throw new Exception('News ID not provided');
        }

        require_once __DIR__ . '/../app/Views/news/detail.php';
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: /MovieHub/movie-website/public/index.php?action=news_list');
    }
    exit();
}

// Fetch latest movies
try {
    $stmt = $pdo->query("SELECT * FROM movies ORDER BY created_at DESC LIMIT 6");
    $latestMovies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch featured movies
    $featuredStmt = $pdo->query("SELECT * FROM movies WHERE is_featured = 1 ORDER BY created_at DESC");
    $featuredMovies = $featuredStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $latestMovies = [];
    $featuredMovies = [];
}

require_once __DIR__ . '/../app/Views/layouts/header.php';
?>

<!-- Hero Section -->
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
            <a href="#movies"
                class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 md:py-3 md:px-8 rounded-full">
                <i class="fas fa-play mr-2"></i>
                Explore Movies
            </a>
        </div>
    </div>
</div>

<!-- Featured Movies Section -->
<section class="py-8 md:py-16 bg-gray-900">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl md:text-3xl font-bold text-white mb-6 md:mb-8">Featured Movies</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($featuredMovies as $movie): ?>
                <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg transform transition duration-300 hover:scale-105">
                    <div class="relative pb-[56.25%]">
                        <img src="/MovieHub/movie-website/public/assets/images/movies/<?php echo htmlspecialchars($movie['image']); ?>"
                            alt="<?php echo htmlspecialchars($movie['title']); ?>"
                            class="absolute top-0 left-0 w-full h-full object-cover">
                    </div>
                    <div class="p-4 md:p-6">
                        <h3 class="text-lg md:text-xl font-bold text-white mb-2">
                            <?php echo htmlspecialchars($movie['title']); ?>
                        </h3>
                        <p class="text-gray-400 mb-4">
                            <?php echo htmlspecialchars($movie['genre']); ?>
                        </p>
                        <div class="flex flex-wrap gap-4">
                            <a href="movie_details.php?id=<?php echo $movie['id']; ?>"
                                class="inline-block bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700 text-sm md:text-base">
                                Watch Now
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Movie Slider Section -->
<section class="py-16 bg-gray-800">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-white mb-8">Trending Now</h2>
        <?php
        // Get movies for slider
        $sliderMoviesQuery = $pdo->query("SELECT * FROM movies WHERE in_slider = 1 ORDER BY created_at DESC");
        $sliderMovies = $sliderMoviesQuery->fetchAll(PDO::FETCH_ASSOC);

        // Pass movies to slider
        $featuredMovies = $sliderMovies; // Variable used in slider.php
        require_once __DIR__ . '/../app/Views/movies/slider.php';
        ?>
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

<style>
    @layer base {
        html {
            @apply antialiased;
        }

        body {
            @apply text-base;
        }
    }

    @layer components {
        .container {
            @apply px-4 mx-auto;
            max-width: 1280px;
        }

        .btn {
            @apply inline-flex items-center justify-center px-4 py-2 rounded-full transition-colors;
        }

        .btn-primary {
            @apply bg-blue-600 text-white hover:bg-blue-700;
        }
    }

    @layer utilities {
        .aspect-video {
            aspect-ratio: 16 / 9;
        }
    }

    /* Mobile-first responsive breakpoints */
    @media (min-width: 640px) {
        .container {
            max-width: 640px;
        }
    }

    @media (min-width: 768px) {
        .container {
            max-width: 768px;
        }
    }

    @media (min-width: 1024px) {
        .container {
            max-width: 1024px;
        }
    }

    @media (min-width: 1280px) {
        .container {
            max-width: 1280px;
        }
    }
</style>