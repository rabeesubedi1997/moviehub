<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../Controllers/NewsController.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /MovieHub/movie-website/app/Views/users/login.php');
    exit();
}

$newsController = new NewsController($pdo);
$article = $newsController->getById($_GET['id']);

if (!$article) {
    header('Location: manage_news.php');
    exit();
}
?>

<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="min-h-screen bg-gray-100">
    <!-- Breadcrumb -->
    <div class="bg-white shadow">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center space-x-2 text-gray-600">
                <a href="dashboard.php" class="hover:text-blue-600">Dashboard</a>
                <span>/</span>
                <a href="manage_news.php" class="hover:text-blue-600">Manage News</a>
                <span>/</span>
                <span class="text-gray-900">Edit News</span>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="md:flex">
                <!-- Left Side - Form -->
                <div class="md:w-2/3 p-6 md:p-8">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Edit Article</h2>
                        <p class="text-sm text-gray-600">Update your article content and settings</p>
                    </div>

                    <form action="/MovieHub/movie-website/public/index.php?action=update_news&id=<?= $article['id'] ?>"
                        method="POST"
                        enctype="multipart/form-data"
                        class="space-y-6"
                        id="newsForm">

                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text"
                                id="title"
                                name="title"
                                required
                                value="<?= htmlspecialchars($article['title']) ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                            <textarea id="content"
                                name="content"
                                rows="12"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?= htmlspecialchars($article['content']) ?></textarea>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="status"
                                name="status"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="draft" <?= $article['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="public" <?= $article['status'] === 'public' ? 'selected' : '' ?>>Public</option>
                                <option value="private" <?= $article['status'] === 'private' ? 'selected' : '' ?>>Private</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <button type="button"
                                onclick="window.location.href='manage_news.php'"
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Update Article
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right Side - Image Upload Preview -->
                <div class="md:w-1/3 bg-gray-50 p-6 md:p-8 border-l">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Featured Image</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <div class="image-preview mb-4">
                                        <?php if ($article['image'] && file_exists(dirname(dirname(dirname(__FILE__))) . "/public/assets/images/news/" . $article['image'])): ?>
                                            <img id="preview"
                                                src="/MovieHub/movie-website/public/assets/images/news/<?= htmlspecialchars($article['image']) ?>"
                                                alt="<?= htmlspecialchars($article['title']) ?>"
                                                class="mx-auto h-48 w-full object-cover rounded">
                                        <?php else: ?>
                                            <img id="preview"
                                                src="/MovieHub/movie-website/public/assets/images/placeholder.jpg"
                                                alt="No image available"
                                                class="mx-auto h-48 w-full object-cover rounded hidden">
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload a file</span>
                                            <input id="image"
                                                name="image"
                                                type="file"
                                                accept="image/*"
                                                class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-md shadow-sm">
                            <h3 class="text-sm font-medium text-gray-900 mb-2">Article Status</h3>
                            <div class="text-sm text-gray-600 space-y-2">
                                <p>Current Status: <span class="font-medium"><?= ucfirst($article['status']) ?></span></p>
                                <p>Created: <?= date('M d, Y', strtotime($article['created_at'])) ?></p>
                                <?php if (isset($article['updated_at'])): ?>
                                    <p>Last Updated: <?= date('M d, Y', strtotime($article['updated_at'])) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('image');
        const imagePreview = document.querySelector('.image-preview');
        const preview = document.getElementById('preview');

        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        });

        // Drag and drop functionality
        const dropZone = document.querySelector('.border-dashed');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropZone.classList.add('border-blue-300', 'bg-blue-50');
        }

        function unhighlight(e) {
            dropZone.classList.remove('border-blue-300', 'bg-blue-50');
        }

        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const file = dt.files[0];

            if (file && file.type.startsWith('image/')) {
                imageInput.files = dt.files;
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        }
    });
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>