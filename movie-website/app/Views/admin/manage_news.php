<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../Controllers/NewsController.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /MovieHub/movie-website/app/Views/users/login.php');
    exit();
}

$newsController = new NewsController($pdo);
$news = $newsController->index();
?>

<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="min-h-screen bg-gray-100">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-900">Manage News</h2>
                <a href="add_news.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
                    <i class="fas fa-plus mr-2"></i>Add News
                </a>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($news as $article): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <?php if ($article['image'] && file_exists(dirname(dirname(dirname(__FILE__))) . "/public/assets/images/news/" . $article['image'])): ?>
                                                <img class="h-10 w-10 rounded-full object-cover"
                                                    src="/MovieHub/movie-website/public/assets/images/news/<?= htmlspecialchars($article['image']) ?>"
                                                    alt="<?= htmlspecialchars($article['title']) ?>"
                                                    onerror="this.src='/MovieHub/movie-website/public/assets/images/placeholder.jpg'">
                                            <?php else: ?>
                                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <i class="fas fa-newspaper text-gray-400"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($article['title']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php echo match ($article['status']) {
                                            'public' => 'bg-green-100 text-green-800',
                                            'private' => 'bg-red-100 text-red-800',
                                            'draft' => 'bg-yellow-100 text-yellow-800',
                                        } ?>">
                                        <?= ucfirst(htmlspecialchars($article['status'])) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('M d, Y', strtotime($article['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="edit_news.php?id=<?= $article['id'] ?>"
                                        class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                    <button onclick="deleteNews(<?= $article['id'] ?>)"
                                        class="text-red-600 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function deleteNews(id) {
        if (confirm('Are you sure you want to delete this news article?')) {
            fetch(`/MovieHub/movie-website/public/index.php?action=delete_news&id=${id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting news article');
                    }
                });
        }
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>