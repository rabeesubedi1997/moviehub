<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../Controllers/NewsController.php';
require_once __DIR__ . '/../layouts/header.php';

$newsController = new NewsController($pdo);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 9;

try {
    $newsData = $newsController->getAllPaginated($page, $limit);
    $news = $newsData['news'] ?? [];
    $totalPages = $newsData['pages'] ?? 0;
} catch (Exception $e) {
    $news = [];
    $totalPages = 0;
}
?>

<div class="min-h-screen bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Movie News & Updates</h1>
        
        <?php if (empty($news)): ?>
            <div class="text-center py-12">
                <p class="text-gray-600">No news articles available at the moment.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($news as $article): 
                    // Ensure all required fields have default values
                    $article = array_merge([
                        'id' => '',
                        'title' => 'Untitled',
                        'content' => '',
                        'image' => '',
                        'author_name' => 'Anonymous',
                        'created_at' => null
                    ], $article);
                ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300">
                        <?php if (!empty($article['image'])): ?>
                            <img src="/MovieHub/movie-website/public/assets/images/news/<?php echo htmlspecialchars($article['image']); ?>"
                                 alt="<?php echo htmlspecialchars($article['title']); ?>"
                                 class="w-full h-48 object-cover">
                        <?php else: ?>
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-400">No image available</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-2">
                                <a href="/MovieHub/movie-website/public/news/article?id=<?php echo htmlspecialchars($article['id']); ?>"
                                   class="hover:text-blue-600 transition duration-300">
                                    <?php echo htmlspecialchars($article['title']); ?>
                                </a>
                            </h2>
                            <div class="text-sm text-gray-500 mb-4">
                                <span>By <?php echo htmlspecialchars($article['author_name']); ?></span>
                                <span class="mx-2">•</span>
                                <span><?php echo $article['created_at'] ? date('M d, Y', strtotime($article['created_at'])) : 'Date not available'; ?></span>
                            </div>
                            <p class="text-gray-600 mb-4">
                                <?php 
                                if (!empty($article['content'])) {
                                    echo htmlspecialchars(substr(strip_tags($article['content']), 0, 150)) . '...';
                                } else {
                                    echo 'No content available';
                                }
                                ?>
                            </p>
                            <a href="/MovieHub/movie-website/public/news/article?id=<?php echo htmlspecialchars($article['id']); ?>"
                               class="inline-block text-blue-600 hover:text-blue-800 transition duration-300">
                                Read More →
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="mt-8 flex justify-center">
                    <div class="flex space-x-2">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>"
                               class="px-4 py-2 border rounded <?php echo $i === $page ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'; ?> transition duration-300">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>