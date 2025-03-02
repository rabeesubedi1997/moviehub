<?php
//session_start();
require_once __DIR__ . '/../../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /MovieHub/movie-website/app/Views/users/login.php');
    exit();
}
?>

<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="bg-gray-100 min-h-screen">
    <!-- Page Header -->
    <div class="bg-white shadow">
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-900">Add New Movie</h1>
                <a href="/MovieHub/movie-website/public/admin/dashboard" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-4xl mx-auto">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form action="/MovieHub/movie-website/public/api/movies/store" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Movie Title</label>
                            <input type="text" id="title" name="title" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="description" name="description" rows="4" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                        </div>

                        <div>
                            <label for="director" class="block text-sm font-medium text-gray-700">Director</label>
                            <input type="text" id="director" name="director" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="release_date" class="block text-sm font-medium text-gray-700">Release Date</label>
                                <input type="date" id="release_date" name="release_date" required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>

                            <div>
                                <label for="genre" class="block text-sm font-medium text-gray-700">Genre</label>
                                <select id="genre" name="genre" required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Select Genre</option>
                                    <option value="Action">Action</option>
                                    <option value="Comedy">Comedy</option>
                                    <option value="Drama">Drama</option>
                                    <option value="Horror">Horror</option>
                                    <option value="Thriller">Thriller</option>
                                    <option value="Sci-Fi">Sci-Fi</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Movie Poster</label>
                            <div class="mt-2 flex items-center space-x-4">
                                <div class="flex-1">
                                    <input type="file" id="image" name="image" accept="image/*" required
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                                </div>
                                <div class="h-32 w-32 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center">
                                    <img id="preview" src="#" alt="Movie poster preview" 
                                         class="hidden h-full w-full object-cover rounded-lg">
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between space-x-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="is_featured" name="is_featured" value="1"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_featured" class="ml-2 block text-sm text-gray-700">Feature Movie</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="in_slider" name="in_slider" value="1"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="in_slider" class="ml-2 block text-sm text-gray-700">Add to Slider</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <button type="button" 
                            onclick="window.location.href='/MovieHub/movie-website/public/admin/dashboard'"
                            class="bg-gray-100 text-gray-700 hover:bg-gray-200 font-bold py-2 px-4 rounded inline-flex items-center">
                        <i class="fas fa-times mr-2"></i> Cancel
                    </button>
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i> Create Movie
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('image');
        const preview = document.getElementById('preview');

        imageInput.onchange = function(evt) {
            const [file] = this.files;
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
            }
        };
    });
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>