<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../Controllers/NewsController.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /MovieHub/movie-website/app/Views/users/login.php');
    exit();
}

// Get news ID from URL
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    header('Location: /MovieHub/movie-website/public/admin/manage-news');
    exit();
}

try {
    $newsController = new NewsController($pdo);
    $article = $newsController->getById($id);

    if (!$article) {
        $_SESSION['error'] = "News article not found";
        header('Location: /MovieHub/movie-website/public/admin/manage-news');
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header('Location: /MovieHub/movie-website/public/admin/manage-news');
    exit();
}
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">Edit News Article</h1>
                <a href="/MovieHub/movie-website/public/admin/manage-news" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to News
                </a>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <form action="/MovieHub/movie-website/public/admin/updated" 
                      method="POST" 
                      enctype="multipart/form-data">
                    
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($article['id']); ?>">

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="title">Title</label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="<?php echo htmlspecialchars($article['title']); ?>" 
                               required 
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="content">Content</label>
                        <textarea id="content" 
                                  name="content" 
                                  rows="10" 
                                  required 
                                  class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500"><?php echo htmlspecialchars($article['content']); ?></textarea>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Image</label>
                        <div class="flex items-center space-x-4">
                            <?php if ($article['image']): ?>
                                <div class="w-32 h-32">
                                    <img src="/MovieHub/movie-website/public/assets/images/news/<?php echo htmlspecialchars($article['image']); ?>"
                                         alt="Current image"
                                         class="w-full h-full object-cover rounded">
                                </div>
                            <?php endif; ?>
                            <input type="file" 
                                   name="image" 
                                   accept="image/*" 
                                   class="border rounded p-2">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="status">Status</label>
                        <select id="status" 
                                name="status" 
                                required 
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                            <option value="draft" <?php echo $article['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                            <option value="public" <?php echo $article['status'] === 'public' ? 'selected' : ''; ?>>Public</option>
                            <option value="private" <?php echo $article['status'] === 'private' ? 'selected' : ''; ?>>Private</option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Update Article
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>