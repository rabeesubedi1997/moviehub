<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /MovieHub/movie-website/app/Views/users/login.php');
    exit();
}
?>

<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="min-h-screen bg-gray-100 py-6 flex flex-col justify-center sm:py-12">
    <div class="relative py-3 sm:max-w-xl md:max-w-2xl lg:max-w-4xl mx-auto">
        <div class="relative px-4 py-10 bg-white mx-8 md:mx-0 shadow rounded-3xl sm:p-10">
            <div class="max-w-md mx-auto">
                <div class="flex items-center space-x-5">
                    <div class="block pl-2 font-semibold text-xl self-start text-gray-700">
                        <h2 class="leading-relaxed">Add New Movie</h2>
                        <p class="text-sm text-gray-500 font-normal leading-relaxed">Enter movie details and upload poster image</p>
                    </div>
                </div>
                <!-- Change the form action -->
                <form action="/MovieHub/movie-website/public/index.php?action=store" method="POST" enctype="multipart/form-data" class="divide-y divide-gray-200">
                    <div class="py-8 text-base leading-6 space-y-6 text-gray-700 sm:text-lg sm:leading-7">
                        <div class="flex flex-col">
                            <label for="title" class="leading-loose">Movie Title</label>
                            <input type="text" id="title" name="title" required
                                class="px-4 py-2 border focus:ring-gray-500 focus:border-gray-900 w-full sm:text-sm border-gray-300 rounded-md focus:outline-none text-gray-600">
                        </div>
                        <div class="flex flex-col">
                            <label for="description" class="leading-loose">Description</label>
                            <textarea id="description" name="description" required rows="4"
                                class="px-4 py-2 border focus:ring-gray-500 focus:border-gray-900 w-full sm:text-sm border-gray-300 rounded-md focus:outline-none text-gray-600"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex flex-col">
                                <label for="release_date" class="leading-loose">Release Date</label>
                                <input type="date" id="release_date" name="release_date" required
                                    class="px-4 py-2 border focus:ring-gray-500 focus:border-gray-900 w-full sm:text-sm border-gray-300 rounded-md focus:outline-none text-gray-600">
                            </div>
                            <div class="flex flex-col">
                                <label for="genre" class="leading-loose">Genre</label>
                                <select id="genre" name="genre" required
                                    class="px-4 py-2 border focus:ring-gray-500 focus:border-gray-900 w-full sm:text-sm border-gray-300 rounded-md focus:outline-none text-gray-600">
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
                        <div class="flex flex-col">
                            <label for="director" class="leading-loose">Director</label>
                            <input type="text" id="director" name="director" required
                                class="px-4 py-2 border focus:ring-gray-500 focus:border-gray-900 w-full sm:text-sm border-gray-300 rounded-md focus:outline-none text-gray-600">
                        </div>
                        <div class="flex flex-col">
                            <label for="image" class="leading-loose">Movie Poster</label>
                            <div class="flex items-center space-x-4">
                                <div class="flex-1">
                                    <input type="file" id="image" name="image" accept="image/*" required
                                        class="px-4 py-2 border focus:ring-gray-500 focus:border-gray-900 w-full sm:text-sm border-gray-300 rounded-md focus:outline-none text-gray-600">
                                </div>
                                <div class="w-24 h-24 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center">
                                    <img id="preview" src="#" alt="Preview" class="hidden max-w-full max-h-full">
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 justify-between">
                            <div class="flex items-center space-x-4">
                                <input type="checkbox" id="is_featured" name="is_featured" value="1"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="is_featured" class="leading-loose">Is a Feature movie?</label>
                            </div>
                            <div class="flex items-center space-x-4">
                                <input type="checkbox" id="in_slider" name="in_slider" value="1"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="in_slider" class="leading-loose">Add to slider</label>
                            </div>
                        </div>
                    </div>
                    <div class="pt-4 flex items-center space-x-4">
                        <button type="button" onclick="window.location.href='dashboard.php'"
                            class="flex justify-center items-center w-full text-gray-900 px-4 py-3 rounded-md focus:outline-none">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg> Cancel
                        </button>
                        <button type="submit"
                            class="bg-blue-500 flex justify-center items-center w-full text-white px-4 py-3 rounded-md focus:outline-none hover:bg-blue-600">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg> Create Movie
                        </button>
                    </div>
                </form>
            </div>
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

<?php require __DIR__ . '/../layouts/footer.php'; ?>