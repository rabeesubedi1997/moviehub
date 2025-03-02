<?php
session_start();
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);

// Add this function before the route handling
function requireAdmin()
{
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        $_SESSION['error'] = "Access denied. Admin privileges required.";
        header('Location: /MovieHub/movie-website/public/login');
        exit();
    }
}

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/Controllers/MovieController.php';
require_once BASE_PATH . '/app/Controllers/NewsController.php';

$movieController = new MovieController($pdo);
$newsController = new NewsController($pdo);

// Basic routing
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/MovieHub/movie-website/public';
$route = str_replace($base_path, '', $request_uri);

// Fetch data for homepage
try {
    $stmt = $pdo->query("SELECT * FROM movies ORDER BY created_at DESC LIMIT 6");
    $latestMovies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $featuredStmt = $pdo->query("SELECT * FROM movies WHERE is_featured = 1 ORDER BY created_at DESC");
    $featuredMovies = $featuredStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $latestMovies = $featuredMovies = [];
}

// Route handling
try {
    switch ($route) {
        // Public routes
        case '/':
        case '/home':
            require BASE_PATH . '/app/Views/home.php';
            break;

        case '/login':
            if (isset($_SESSION['user_id'])) {
                header('Location: /MovieHub/movie-website/public/');
                exit();
            }
            require BASE_PATH . '/app/Views/users/login.php';
            break;

        case '/register':
            require BASE_PATH . '/app/Views/users/register.php';
            break;

        case '/logout':
            session_destroy();
            header('Location: /MovieHub/movie-website/public/login');
            exit();
            break;

        // Admin routes
        case '/admin/dashboard':
            requireAdmin();
            require BASE_PATH . '/app/Views/admin/dashboard.php';
            break;

        case '/admin/add-movie':
            requireAdmin();
            require BASE_PATH . '/app/Views/admin/add_movie.php';
            break;

        case '/admin/edit-movie':
            requireAdmin();
            try {
                $movieId = $_GET['id'] ?? null;
                if (!$movieId) {
                    throw new Exception("Movie ID is required");
                }

                $stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
                $stmt->execute([$movieId]);
                $movie = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$movie) {
                    throw new Exception("Movie not found");
                }

                require BASE_PATH . '/app/Views/admin/edit_movie.php';
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /MovieHub/movie-website/public/admin/dashboard');
                exit();
            }
            break;

        case '/admin/add-news':
            requireAdmin();
            require BASE_PATH . '/app/Views/admin/add_news.php';
            break;

        case '/admin/edit-news':
            requireAdmin();
            $id = $_GET['id'] ?? null;
            if (!$id) {
                header('Location: /MovieHub/movie-website/public/admin/manage-news');
                exit();
            }
            $newsController = new NewsController($pdo);
            $article = $newsController->getById($id);
            if (!$article) {
                $_SESSION['error'] = "News article not found";
                header('Location: /MovieHub/movie-website/public/admin/manage-news');
                exit();
            }
            require BASE_PATH . '/app/Views/admin/edit_news.php';
            break;

        case '/admin/news':
            requireAdmin();
            $newsController = new NewsController($pdo);
            $news = $newsController->getAll();
            require BASE_PATH . '/app/Views/admin/news/index.php';
            break;

        case '/admin/news/add':
            requireAdmin();
            require BASE_PATH . '/app/Views/admin/news/add.php';
            break;

        case '/admin/news/edit':
            requireAdmin();
            $id = $_GET['id'] ?? null;
            if (!$id) {
                $_SESSION['error'] = "News ID is required";
                header('Location: /MovieHub/movie-website/public/admin/news');
                exit();
            }
            $newsController = new NewsController($pdo);
            $news = $newsController->getById($id);
            require BASE_PATH . '/app/Views/admin/news/edit.php';
            break;

        // API routes for CRUD operations
        case '/api/movies/store':
            requireAdmin();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    $movieController = new MovieController($pdo);
                    $result = $movieController->store($_POST, $_FILES['image']);

                    if ($result) {
                        $_SESSION['success'] = "Movie added successfully!";
                        header('Location: /MovieHub/movie-website/public/admin/dashboard');
                        exit();
                    } else {
                        throw new Exception("Failed to add movie");
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = "Error: " . $e->getMessage();
                    header('Location: /MovieHub/movie-website/public/admin/add-movie');
                    exit();
                }
            }
            break;

        case '/api/movies/update':
            requireAdmin();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    $id = $_POST['id'] ?? null;
                    $title = $_POST['title'] ?? '';
                    $description = $_POST['description'] ?? '';
                    $director = $_POST['director'] ?? '';
                    $release_date = $_POST['release_date'] ?? '';
                    $genre = $_POST['genre'] ?? '';
                    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

                    if (!$id || !$title || !$description || !$director || !$release_date || !$genre) {
                        throw new Exception("All fields are required");
                    }

                    $updateData = [
                        'title' => $title,
                        'description' => $description,
                        'director' => $director,
                        'release_date' => $release_date,
                        'genre' => $genre,
                        'is_featured' => $is_featured
                    ];

                    // Handle image upload if provided
                    if (!empty($_FILES['image']['name'])) {
                        $uploadDir = PUBLIC_PATH . '/assets/images/movies/';
                        $fileName = time() . '_' . basename($_FILES['image']['name']);
                        $targetPath = $uploadDir . $fileName;

                        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                            $updateData['image'] = $fileName;
                        }
                    }

                    // Build update query
                    $setClauses = [];
                    $params = [];
                    foreach ($updateData as $key => $value) {
                        $setClauses[] = "$key = ?";
                        $params[] = $value;
                    }
                    $params[] = $id; // Add ID for WHERE clause

                    $sql = "UPDATE movies SET " . implode(', ', $setClauses) . " WHERE id = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);

                    $_SESSION['success'] = "Movie updated successfully";
                    header('Location: /MovieHub/movie-website/public/admin/dashboard');
                    exit();
                } catch (Exception $e) {
                    $_SESSION['error'] = $e->getMessage();
                    header("Location: /MovieHub/movie-website/app/Views/admin/edit_movie.php?id=$id");
                    exit();
                }
            }
            break;

        case '/api/movies/delete':
            requireAdmin();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    $movieId = $_POST['id'] ?? null;
                    if (!$movieId) {
                        throw new Exception("Movie ID is required");
                    }

                    $movieController->delete($movieId);
                    $_SESSION['success'] = "Movie deleted successfully";
                } catch (Exception $e) {
                    $_SESSION['error'] = "Error deleting movie: " . $e->getMessage();
                }
                header('Location: /MovieHub/movie-website/public/admin/dashboard');
                exit();
            }
            break;

        case '/api/news/store':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $newsController->store($_POST, $_FILES['image'] ?? null);
            }
            break;

        case '/api/news/update':
            requireAdmin();
            try {
                $newsController = new NewsController($pdo);
                $newsController->update($_POST['id'], $_POST, $_FILES['image'] ?? null);
                $_SESSION['success'] = "News article updated successfully";
                header('Location: /MovieHub/movie-website/public/admin/manage-news');
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /MovieHub/movie-website/public/admin/edit-news?id=' . $_POST['id']);
            }
            exit();
            break;

        case '/api/news/delete':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $newsController->delete($_POST['id']);
            }
            break;

        // Add this case to your switch statement
        case '/news':
            require_once BASE_PATH . '/app/Controllers/NewsController.php';
            $newsController = new NewsController($pdo);
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 9;
            try {
                $newsData = $newsController->getAllPaginated($page, $limit);
                $news = $newsData['news'];
                $totalPages = $newsData['pages'];
            } catch (Exception $e) {
                $news = [];
                $totalPages = 0;
            }
            require BASE_PATH . '/app/Views/news/index.php';
            break;

        // Add this case to your switch statement
        case '/news/article':
            require_once BASE_PATH . '/app/Controllers/NewsController.php';
            $newsController = new NewsController($pdo);
            $slug = $_GET['slug'] ?? null;
            
            if (!$slug) {
                header('Location: /MovieHub/movie-website/public/news');
                exit();
            }
            
            $article = $newsController->getBySlug($slug);
            if ($article) {
                $newsController->incrementViews($article['id']);
            }
            require BASE_PATH . '/app/Views/news/article.php';
            break;

        // Add this case to your switch statement
        case '/news/article':
            require_once BASE_PATH . '/app/Controllers/NewsController.php';
            $newsController = new NewsController($pdo);
            $id = $_GET['id'] ?? null;
            
            if (!$id) {
                header('Location: /MovieHub/movie-website/public/news');
                exit();
            }
            
            $article = $newsController->getById($id);
            if ($article) {
                $newsController->incrementViews($article['id']);
            }
            require BASE_PATH . '/app/Views/news/detail.php';
            break;

        // Add this case to your switch statement
        case '/admin/manage-news':
            requireAdmin();
            require BASE_PATH . '/app/Views/admin/manage_news.php';
            break;

        // Add these cases to your switch statement
        case '/api/news/create':
            requireAdmin();
            try {
                $newsController = new NewsController($pdo);
                $newsController->create($_POST, $_FILES['image'] ?? null);
                $_SESSION['success'] = "News article created successfully";
                header('Location: /MovieHub/movie-website/public/admin/manage-news');
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /MovieHub/movie-website/public/admin/add-news');
            }
            exit();
            break;

        case '/api/news/delete':
            requireAdmin();
            try {
                $newsController = new NewsController($pdo);
                $newsController->delete($_POST['id']);
                $_SESSION['success'] = "News article deleted successfully";
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            header('Location: /MovieHub/movie-website/public/admin/manage-news');
            exit();
            break;

        case '/admin/update-news':
            requireAdmin();
            try {
                $newsController = new NewsController($pdo);
                if ($newsController->update($_POST['id'], $_POST, $_FILES['image'] ?? null)) {
                    $_SESSION['success'] = "News article updated successfully";
                } else {
                    $_SESSION['error'] = "Failed to update news article";
                }
            } catch (Exception $e) {
                $_SESSION['error'] = "Error: " . $e->getMessage();
            }
            header('Location: /MovieHub/movie-website/public/admin/manage-news');
            exit();
            break;

        case '/admin/updated':
            requireAdmin();
            require BASE_PATH . '/app/Views/admin/updated.php';
            break;

        default:
            header("HTTP/1.0 404 Not Found");
            require BASE_PATH . '/app/Views/404.php';
            break;
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    $_SESSION['error'] = "An error occurred. Please try again later.";
    header('Location: /MovieHub/movie-website/public/');
    exit();
}
