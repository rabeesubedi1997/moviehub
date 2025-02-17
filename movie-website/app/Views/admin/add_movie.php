<?php require __DIR__ . '/../layouts/header.php'; ?>
<h1 class="text-3xl font-bold mb-4">Add Movie</h1>
<form action="/MovieHub/movie-website/public/index.php?url=store" method="POST" class="space-y-4">
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Title:</label>
        <input type="text" id="title" name="title" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
    </div>
    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Description:</label>
        <textarea id="description" name="description" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
    </div>
    <div>
        <label for="release_date" class="block text-sm font-medium text-gray-700">Release Date:</label>
        <input type="date" id="release_date" name="release_date" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
    </div>
    <div>
        <label for="genre" class="block text-sm font-medium text-gray-700">Genre:</label>
        <input type="text" id="genre" name="genre" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
    </div>
    <div>
        <label for="director" class="block text-sm font-medium text-gray-700">Director:</label>
        <input type="text" id="director" name="director" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
    </div>
    <div>
        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Add Movie</button>
    </div>
</form>
<?php require __DIR__ . '/../layouts/footer.php'; ?>