<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /MovieHub/movie-website/app/Views/users/login.php');
    exit();
}

// Get movie ID from URL
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    header('Location: /MovieHub/movie-website/app/Views/admin/dashboard.php');
    exit();
}

try {
    // Fetch movie data
    $stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->execute([$id]);
    $movie = $stmt->fetch();

    if (!$movie) {
        $_SESSION['error'] = "Movie not found";
        header('Location: /MovieHub/movie-website/app/Views/admin/dashboard.php');
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header('Location: dashboard.php');
    exit();
}
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">Edit Movie</h1>
                <a href="/MovieHub/movie-website/public/admin/dashboard"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <form action="/MovieHub/movie-website/public/api/movies/update" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      class="space-y-6">

                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($movie['id']); ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">Movie Title</label>
                                <input type="text" id="title" name="title" 
                                       value="<?php echo htmlspecialchars($movie['title']); ?>" required
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea id="description" name="description" rows="4" required
                                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"><?php echo htmlspecialchars($movie['description']); ?></textarea>
                            </div>

                            <div>
                                <label for="director" class="block text-sm font-medium text-gray-700">Director</label>
                                <input type="text" id="director" name="director" 
                                       value="<?php echo htmlspecialchars($movie['director']); ?>" required
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="release_date" class="block text-sm font-medium text-gray-700">Release Date</label>
                                    <input type="date" id="release_date" name="release_date" 
                                           value="<?php echo htmlspecialchars($movie['release_date']); ?>" required
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                                </div>

                                <div>
                                    <label for="genre" class="block text-sm font-medium text-gray-700">Genre</label>
                                    <input type="text" id="genre" name="genre" 
                                           value="<?php echo htmlspecialchars($movie['genre']); ?>" required
                                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Movie Poster</label>
                                <div class="mt-2 flex items-center space-x-4">
                                    <div class="flex-1">
                                        <input type="file" id="image" name="image" accept="image/*"
                                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                                    </div>
                                    <div class="h-32 w-32 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center">
                                        <?php if ($movie['image']): ?>
                                            <img id="preview" src="/MovieHub/movie-website/public/assets/images/movies/<?php echo htmlspecialchars($movie['image']); ?>"
                                                 alt="Movie poster preview"
                                                 class="h-full w-full object-cover rounded-lg">
                                        <?php else: ?>
                                            <img id="preview" src="#" alt="Movie poster preview" 
                                                 class="hidden h-full w-full object-cover rounded-lg">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_featured" value="1"
                                           <?php echo $movie['is_featured'] ? 'checked' : ''; ?>
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Feature Movie</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 pt-6 border-t">
                        <button type="button" 
                                onclick="window.location.href='/MovieHub/movie-website/public/admin/dashboard'"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </button>
                        <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                            <i class="fas fa-save mr-2"></i> Update Movie
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    const preview = document.getElementById('preview');
    
    // Show existing image if available
    if (preview.getAttribute('src') !== '#') {
        preview.classList.remove('hidden');
    }

    imageInput.onchange = function(evt) {
        const [file] = this.files;
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
        }
    };
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>