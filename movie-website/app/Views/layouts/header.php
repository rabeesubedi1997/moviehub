<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>MovieHub Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
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
                <a href="/MovieHub/movie-website/public/index.php" class="text-2xl font-bold text-blue-500">Movie<span class="text-white">Hub</span></a>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/MovieHub/movie-website/public/index.php" class="<?php echo $route === '/' ? 'text-blue-500' : 'text-gray-500 hover:text-gray-900'; ?>">Home</a>
                    <a href="/MovieHub/movie-website/public/movies" class="<?php echo $route === '/movies' ? 'text-blue-500' : 'text-gray-500 hover:text-gray-900'; ?>">Movies</a>
                    <a href="/MovieHub/movie-website/public/news" class="<?php echo $route === '/news' ? 'text-blue-500' : 'text-gray-500 hover:text-gray-900'; ?>">News</a>
                    <a href="/MovieHub/movie-website/public/about" class="hover:text-blue-500">About</a>
                    <a href="/MovieHub/movie-website/public/contact" class="hover:text-blue-500">Contact</a>

                    <!-- Authentication Links -->
                    <div class="flex items-center space-x-4">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <a href="/MovieHub/movie-website/public/admin/dashboard"
                                    class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-full">
                                    <i class="fas fa-user-shield mr-2"></i>Admin Dashboard
                                </a>
                            <?php endif; ?>
                            <a href="/MovieHub/movie-website/public/logout"
                                class="text-white hover:text-gray-300">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        <?php else: ?>
                            <a href="/MovieHub/movie-website/public/login"
                                class="text-white hover:text-gray-300">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login
                            </a>
                            <a href="/MovieHub/movie-website/public/register"
                                class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-full">
                                <i class="fas fa-user-plus mr-2"></i>Register
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="p-4 focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="mobile-menu md:hidden bg-gray-900 text-white">
        <div class="px-4 py-2 space-y-4">
            <a href="/MovieHub/movie-website/public/index.php" class="block hover:text-blue-500">Home</a>
            <a href="/MovieHub/movie-website/public/movies" class="block hover:text-blue-500">Movies</a>
            <a href="/MovieHub/movie-website/public/news" class="block hover:text-blue-500">News</a>
            <a href="/MovieHub/movie-website/public/about" class="block hover:text-blue-500">About</a>
            <a href="/MovieHub/movie-website/public/contact" class="block hover:text-blue-500">Contact</a>

            <!-- Mobile Authentication Links -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="/MovieHub/movie-website/public/admin/dashboard"
                        class="block bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-full text-center mb-2">
                        <i class="fas fa-user-shield mr-2"></i>Dashboard
                    </a>
                <?php endif; ?>
                <a href="/MovieHub/movie-website/public/logout"
                    class="block text-red-500 hover:text-red-600">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            <?php else: ?>
                <div class="space-y-2">
                    <a href="/MovieHub/movie-website/public/login"
                        class="block bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-full text-center">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                    <a href="/MovieHub/movie-website/public/register"
                        class="block bg-green-600 hover:bg-green-700 px-4 py-2 rounded-full text-center">
                        <i class="fas fa-user-plus mr-2"></i>Register
                    </a>
                </div>
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