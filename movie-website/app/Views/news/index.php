<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../layouts/header.php';

try {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 9;
    $offset = ($page - 1) * $limit;

    $stmt = $pdo->prepare("SELECT * FROM news WHERE status = 'public' ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalStmt = $pdo->query("SELECT COUNT(*) FROM news WHERE status = 'public'");
    $total = $totalStmt->fetchColumn();
    $totalPages = ceil($total / $limit);
} catch (PDOException $e) {
    $news = [];
    $totalPages = 0;
}
?>

<div class="min-h-screen bg-gray-900 py-12">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold text-white mb-8">Movie News & Updates</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($news as $article): ?>
                <article class="bg-gray-800 rounded-lg overflow-hidden shadow-lg transform transition duration-300 hover:scale-105">
                    <img src="/MovieHub/movie-website/public/assets/images/news/<?php echo htmlspecialchars($article['image']); ?>"
                        alt="<?php echo htmlspecialchars($article['title']); ?>"
                        class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-white mb-2">
                            <?php echo htmlspecialchars($article['title']); ?>
                        </h2>
                        <p class="text-gray-400 mb-4 line-clamp-3">
                            <?php echo htmlspecialchars(substr($article['content'], 0, 200)) . '...'; ?>
                        </p>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">
                                <?php echo date('M d, Y', strtotime($article['created_at'])); ?>
                            </span>
                            <a href="/MovieHub/movie-website/public/index.php?action=news_detail&id=<?php echo $article['id']; ?>"
                                class="inline-block bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700">
                                Read More
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="mt-12 flex justify-center space-x-2">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="/MovieHub/movie-website/public/index.php?action=news_list&page=<?php echo $i; ?>"
                        class="px-4 py-2 rounded-full <?php echo $page === $i ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>