<?php
//session_start();
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../Controllers/NewsController.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /MovieHub/movie-website/public/login');
    exit();
}

$newsController = new NewsController($pdo);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;

try {
    $newsData = $newsController->getAllPaginatedAdmin($page, $limit);
    $news = $newsData['news'];
    $totalPages = $newsData['pages'];
} catch (Exception $e) {
    $_SESSION['error'] = "Error loading news: " . $e->getMessage();
    $news = [];
    $totalPages = 0;
}
?>

<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="min-h-screen flex">
    <!-- Sidebar -->
    <div class="bg-blue-800 w-64 space-y-6 py-7 px-2 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out">
        <!-- Include your sidebar content here -->
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
            <a href="/MovieHub/movie-website/public/admin/manage-news" class="block py-2.5 px-4 rounded transition duration-200 bg-blue-700">
                Manage News
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">Manage News</h1>
                <a href="/MovieHub/movie-website/public/admin/add-news" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-plus mr-2"></i>Add News Article
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
            <div class="container mx-auto px-4 py-8">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($news as $article): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($article['title']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php echo $article['status'] === 'public' ? 'bg-green-100 text-green-800' : 
                                                    ($article['status'] === 'draft' ? 'bg-yellow-100 text-yellow-800' : 
                                                    'bg-gray-100 text-gray-800'); ?>">
                                            <?php echo ucfirst($article['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($article['author_name']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('M d, Y', strtotime($article['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="/MovieHub/movie-website/public/admin/edit-news?id=<?php echo htmlspecialchars($article['id']); ?>" 
                                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded mr-2">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </a>
                                        <form action="/MovieHub/movie-website/public/api/news/delete" method="POST" class="inline-block">
                                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($article['id']); ?>">
                                            <button type="submit"
                                                    onclick="return confirm('Are you sure you want to delete this article? This action cannot be undone.')"
                                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded">
                                                <i class="fas fa-trash-alt mr-1"></i>Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php if ($totalPages > 1): ?>
                        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                            <div class="flex justify-center">
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <a href="?page=<?php echo $i; ?>" 
                                           class="relative inline-flex items-center px-4 py-2 border text-sm font-medium 
                                           <?php echo $i === $page ? 
                                                'z-10 bg-blue-50 border-blue-500 text-blue-600' : 
                                                'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>
                                </nav>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>