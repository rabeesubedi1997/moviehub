<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MovieHub - Welcome</title>
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
    </style>
</head>

<body>
    <header class="bg-gray-900 text-white border-b border-gray-800">
        <!-- Top Navigation -->
        <nav class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="/MovieHub/movie-website/public/index.php" class="text-2xl font-bold text-blue-500">Movie<span class="text-white">Hub</span></a>
                <div class="flex items-center space-x-8">
                    <a href="/MovieHub/movie-website/public/index.php" class="nav-link">Home</a>
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
                    <a href="/MovieHub/movie-website/app/Views/users/login.php" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-full">
                        <i class="fas fa-user-shield mr-2"></i>Admin
                    </a>
                </div>
            </div>
        </nav>
    </header>
</body>

</html>