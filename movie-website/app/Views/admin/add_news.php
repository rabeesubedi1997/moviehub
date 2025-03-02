<?php
//session_start();
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../Controllers/NewsController.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /MovieHub/movie-website/public/login');
    exit();
}

$newsId = $_GET['id'] ?? null;
$article = null;

if ($newsId) {
    $newsController = new NewsController($pdo);
    $article = $newsController->getById($newsId);
}
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="min-h-screen bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold"><?php echo $newsId ? 'Edit' : 'Add'; ?> News Article</h1>
                <a href="/MovieHub/movie-website/public/admin/manage-news" 
                   class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded">
                    Back to News
                </a>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form action="/MovieHub/movie-website/public/api/news/<?php echo $newsId ? 'update' : 'create'; ?>" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                
                <?php if ($newsId): ?>
                    <input type="hidden" name="id" value="<?php echo $newsId; ?>">
                <?php endif; ?>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                        Title
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           required 
                           value="<?php echo htmlspecialchars($article['title'] ?? ''); ?>"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="content">
                        Content
                    </label>
                    <textarea id="content" 
                              name="content" 
                              rows="10" 
                              required 
                              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo htmlspecialchars($article['content'] ?? ''); ?></textarea>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Featured Image
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <?php if (isset($article['image']) && $article['image']): ?>
                                <img src="/MovieHub/movie-website/public/assets/images/news/<?php echo htmlspecialchars($article['image']); ?>"
                                     alt="Current image"
                                     id="preview"
                                     class="mx-auto h-32 w-auto mb-4">
                            <?php else: ?>
                                <img id="preview" src="#" alt="" class="mx-auto h-32 w-auto hidden">
                            <?php endif; ?>
                            
                            <div class="flex text-sm text-gray-600">
                                <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                    <span>Upload a file</span>
                                    <input id="image" 
                                           name="image" 
                                           type="file" 
                                           class="sr-only" 
                                           accept="image/*"
                                           <?php echo !$newsId ? 'required' : ''; ?>>
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                        Status
                    </label>
                    <select id="status" 
                            name="status" 
                            required 
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="draft" <?php echo (isset($article['status']) && $article['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                        <option value="private" <?php echo (isset($article['status']) && $article['status'] === 'private') ? 'selected' : ''; ?>>Private</option>
                        <option value="public" <?php echo (isset($article['status']) && $article['status'] === 'public') ? 'selected' : ''; ?>>Public</option>
                    </select>
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        <?php echo $newsId ? 'Update' : 'Publish'; ?> Article
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('image').onchange = function(evt) {
    const [file] = this.files;
    if (file) {
        const preview = document.getElementById('preview');
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
    }
};
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>