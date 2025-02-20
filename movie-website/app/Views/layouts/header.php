<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>MovieHub</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Add custom styles -->
    <style>
        .mega-menu {
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            width: 100vw;
            background: rgba(17, 24, 39, 0.98);
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(8px);
            border-top: 1px solid rgba(59, 130, 246, 0.5);
            z-index: 1000;
        }

        .menu-item {
            position: relative;
        }

        .menu-item:hover .mega-menu {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }

        .mega-menu-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .menu-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #3b82f6;
            display: inline-block;
        }

        .menu-list {
            list-style: none;
            padding: 0;
        }

        .menu-list li {
            margin-bottom: 0.75rem;
        }

        .menu-list a {
            color: #9ca3af;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .menu-list a:hover {
            color: #3b82f6;
            transform: translateX(5px);
        }

        .featured-movie {
            position: relative;
            border-radius: 0.5rem;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .featured-movie:hover {
            transform: translateY(-5px);
        }

        .featured-movie img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .featured-movie-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9), transparent);
        }

        .nav-link {
            position: relative;
            padding: 0.5rem 1rem;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            width: 0;
            height: 2px;
            background: #3b82f6;
            transition: all 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
            left: 0;
        }

        .dropdown-icon {
            transition: transform 0.3s ease;
        }

        .menu-item:hover .dropdown-icon {
            transform: rotate(180deg);
        }

        .slider {
            position: relative;
            width: 100%;
            height: 500px;
            overflow: hidden;
            margin-top: 2rem;
        }

        .slider-container {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .slider-item {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        .slider-item.active {
            opacity: 1;
        }

        .slider-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .slider-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 2rem;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            color: white;
        }

        .slider-controls {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            z-index: 10;
            display: flex;
            justify-content: space-between;
            padding: 0 1rem;
        }

        .slider-btn {
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 1rem;
            cursor: pointer;
            border-radius: 50%;
            transition: background 0.3s ease;
        }

        .slider-btn:hover {
            background: rgba(0, 0, 0, 0.8);
        }

        .slider-indicators {
            position: absolute;
            bottom: 1rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 0.5rem;
            z-index: 10;
        }

        .indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .indicator.active {
            background: white;
        }

        @media (max-width: 768px) {
            .mobile-menu {
                display: none;
            }

            .mobile-menu.active {
                display: block;
            }
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Mobile Navigation Toggle -->
    <div class="md:hidden">
        <button id="mobile-menu-button" class="p-4 focus:outline-none">
            <i class="fas fa-bars text-2xl"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="bg-gray-900 text-white">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <a href="/MovieHub/movie-website/public/index.php" target="_blank" class="text-2xl font-bold text-blue-500">Movie<span class="text-white">Hub</span></a>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/MovieHub/movie-website/public/index.php" class="hover:text-blue-500">Home</a>
                    <a href="/MovieHub/movie-website/public/movies.php" class="hover:text-blue-500">Movies</a>
                    <a href="/MovieHub/movie-website/app/Views/news/index.php" class="hover:text-blue-500">News</a>
                    <div class="menu-item">
                        <a href="#movies" class="nav-link flex items-center gap-2">
                            Movies
                            <i class="fas fa-chevron-down text-xs dropdown-icon"></i>
                        </a>
                        <div class="mega-menu">
                            <div class="mega-menu-content">
                                <div class="grid grid-cols-12 gap-8">
                                    <div class="col-span-3">
                                        <h3 class="menu-title">Latest Releases</h3>
                                        <ul class="menu-list">
                                            <li>
                                                <a href="#">
                                                    <i class="fas fa-film text-blue-500"></i>
                                                    New Releases
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    <i class="fas fa-calendar text-blue-500"></i>
                                                    Coming Soon
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    <i class="fas fa-star text-blue-500"></i>
                                                    Top Rated
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-span-3">
                                        <h3 class="menu-title">Genres</h3>
                                        <ul class="menu-list">
                                            <li>
                                                <a href="#">
                                                    <i class="fas fa-running text-blue-500"></i>
                                                    Action
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    <i class="fas fa-theater-masks text-blue-500"></i>
                                                    Drama
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    <i class="fas fa-laugh text-blue-500"></i>
                                                    Comedy
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-span-6">
                                        <h3 class="menu-title">Featured Movies</h3>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="featured-movie">
                                                <img src="/MovieHub/movie-website/public/assets/images/movies/featured1.jpg"
                                                    alt="Featured Movie">
                                                <div class="featured-movie-overlay">
                                                    <h4 class="text-white text-lg font-semibold">Movie Title</h4>
                                                    <p class="text-gray-300 text-sm">Action, Drama</p>
                                                </div>
                                            </div>
                                            <div class="featured-movie">
                                                <img src="/MovieHub/movie-website/public/assets/images/movies/featured2.jpg"
                                                    alt="Featured Movie">
                                                <div class="featured-movie-overlay">
                                                    <h4 class="text-white text-lg font-semibold">Movie Title</h4>
                                                    <p class="text-gray-300 text-sm">Thriller, Mystery</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative menu-item">
                        <a href="#about" class="nav-link">About</a>
                        <div class="mega-menu py-8">
                            <div class="container mx-auto grid grid-cols-3 gap-8">
                                <div>
                                    <h3 class="menu-title">Company</h3>
                                    <ul class="menu-list">
                                        <li><a href="#" class="hover:text-blue-400">Our Story</a></li>
                                        <li><a href="#" class="hover:text-blue-400">Team</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="#news" class="nav-link">News</a>
                    <a href="#contact" class="nav-link">Contact</a>
                    <!-- Replace the admin link -->
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="/MovieHub/movie-website/app/Views/admin/dashboard.php" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-full">
                            <i class="fas fa-user-shield mr-2"></i>Admin Dashboard
                        </a>
                    <?php else: ?>
                        <a href="/MovieHub/movie-website/app/Views/users/login.php" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-full">
                            <i class="fas fa-user-shield mr-2"></i>Admin Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="mobile-menu md:hidden bg-gray-900 text-white">
        <div class="px-4 py-2 space-y-4">
            <a href="/MovieHub/movie-website/public/index.php" class="block hover:text-blue-500">Home</a>
            <a href="#movies" class="block hover:text-blue-500">Movies</a>
            <a href="#about" class="block hover:text-blue-500">About</a>
            <a href="#contact" class="block hover:text-blue-500">Contact</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="/MovieHub/movie-website/app/Views/admin/dashboard.php"
                        class="block bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-full text-center">Dashboard</a>
                <?php endif; ?>
                <a href="/MovieHub/movie-website/app/Views/users/logout.php"
                    class="block text-red-500 hover:text-red-600">Logout</a>
            <?php else: ?>
                <a href="/MovieHub/movie-website/app/Views/users/login.php"
                    class="block bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-full text-center">Login</a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('active');
            });
        });
    </script>
</body>

</html>