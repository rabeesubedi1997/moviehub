<?php
//session_start();
require_once __DIR__ . '/../../../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /MovieHub/movie-website/app/Views/users/login.php');
    exit();
}

try {
    // Get user's information
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    // Get total movies count
    $movieQuery = $pdo->query("SELECT COUNT(*) as total FROM movies");
    $totalMovies = $movieQuery->fetch(PDO::FETCH_ASSOC)['total'];

    // Get total users count
    $userQuery = $pdo->query("SELECT COUNT(*) as total FROM users");
    $totalUsers = $userQuery->fetch(PDO::FETCH_ASSOC)['total'];

    // Get recent movies with more details
    $recentMoviesQuery = $pdo->query("
        SELECT 
            id,
            title,
            description,
            release_date,
            genre,
            director,
            created_at
        FROM movies 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $recentMovies = $recentMoviesQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
    $totalMovies = 0;
    $totalUsers = 0;
    $recentMovies = [];
}
?>

<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    <!-- Success Message -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $_SESSION['success']; ?></span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg onclick="this.parentElement.parentElement.remove()" 
                     class="fill-current h-6 w-6 text-green-500 cursor-pointer" 
                     role="button" 
                     xmlns="http://www.w3.org/2000/svg" 
                     viewBox="0 0 20 20">
                    <title>Close</title>
                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                </svg>
            </span>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Error Message -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $_SESSION['error']; ?></span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg onclick="this.parentElement.parentElement.remove()" 
                     class="fill-current h-6 w-6 text-red-500 cursor-pointer" 
                     role="button" 
                     xmlns="http://www.w3.org/2000/svg" 
                     viewBox="0 0 20 20">
                    <title>Close</title>
                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                </svg>
            </span>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="bg-blue-800 w-64 space-y-6 py-7 px-2 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out">
            <div class="flex items-center space-x-2 px-4">
                <span class="text-white text-2xl font-extrabold">MovieHub</span>
            </div>

            <nav class="text-white space-y-2 px-4">
                <a href="/MovieHub/movie-website/public/admin/dashboard" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700">
                    Dashboard
                </a>
                <a href="/MovieHub/movie-website/public/admin/add-movie" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700">
                    Add Movie
                </a>
                <a href="/MovieHub/movie-website/public/admin/add-news" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700">
                    Add News
                </a>
                <a href="/MovieHub/movie-website/public/admin/manage-news" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-blue-700">
                    Manage News
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                    <div class="flex items-center space-x-4">
                        <a href="/MovieHub/movie-website/public/admin/add-movie" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-plus mr-2"></i>Add Movie
                        </a>
                        <a href="/MovieHub/movie-website/public/" target="_blank" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-external-link-alt mr-2"></i>View Site
                        </a>
                        <a href="/MovieHub/movie-website/public/logout" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </a>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                    <!-- Stats -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">
                                                Total Movies
                                            </dt>
                                            <dd class="text-lg font-bold text-gray-900">
                                                <?php echo $totalMovies; ?>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">
                                                Total Users
                                            </dt>
                                            <dd class="text-lg font-bold text-gray-900">
                                                <?php echo $totalUsers; ?>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Movies Table -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Recent Movies
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Genre</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Release Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($recentMovies as $movie): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($movie['title']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?php echo htmlspecialchars($movie['genre']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?php echo htmlspecialchars($movie['release_date']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="/MovieHub/movie-website/app/Views/admin/edit_movie.php?id=<?php echo $movie['id']; ?>"
                                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded mr-2">
                                                    <i class="fas fa-edit mr-1"></i>Edit
                                                </a>
                                                <form action="/MovieHub/movie-website/public/api/movies/delete" method="POST" class="inline-block">
                                                    <input type="hidden" name="id" value="<?php echo $movie['id']; ?>">
                                                    <button type="submit"
                                                        onclick="return confirm('Are you sure you want to delete this movie?')"
                                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded">
                                                        <i class="fas fa-trash-alt mr-1"></i>Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($recentMovies)): ?>
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                                No movies found
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>