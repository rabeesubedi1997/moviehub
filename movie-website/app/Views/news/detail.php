<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../layouts/header.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

try {
    // Get current article
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ? AND status = 'public'");
    $stmt->execute([$_GET['id']]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$article) {
        header('Location: index.php');
        exit();
    }

    // Get related news
    $relatedStmt = $pdo->prepare("
        SELECT * FROM news 
        WHERE status = 'public' 
        AND id != ? 
        ORDER BY created_at DESC 
        LIMIT 3
    ");
    $relatedStmt->execute([$_GET['id']]);
    $relatedNews = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header('Location: index.php');
    exit();
}

// Generate sharing URLs
$shareUrl = urlencode("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
$shareTitle = urlencode($article['title']);
$shareFacebook = "https://www.facebook.com/sharer/sharer.php?u={$shareUrl}";
$shareTwitter = "https://twitter.com/intent/tweet?url={$shareUrl}&text={$shareTitle}";
$shareLinkedIn = "https://www.linkedin.com/sharing/share-offsite/?url={$shareUrl}";
?>

<div class="min-h-screen bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <?php if (!$article): ?>
            <div class="text-center py-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Article Not Found</h2>
                <p class="text-gray-600 mb-4">The article you're looking for doesn't exist or has been removed.</p>
                <a href="/MovieHub/movie-website/public/news" 
                   class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300">
                    Back to News
                </a>
            </div>
        <?php else: ?>
            <!-- Breadcrumb -->
            <nav class="mb-8">
                <ol class="flex items-center space-x-2 text-sm text-gray-500">
                    <li><a href="/MovieHub/movie-website/public/" class="hover:text-gray-900">Home</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li><a href="/MovieHub/movie-website/public/news" class="hover:text-gray-900">News</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li class="text-gray-900"><?php echo htmlspecialchars($article['title']); ?></li>
                </ol>
            </nav>

            <div class="max-w-4xl mx-auto">
                <article class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <!-- Article Header -->
                    <header class="p-8">
                        <h1 class="text-4xl font-bold text-gray-900 mb-4">
                            <?php echo htmlspecialchars($article['title']); ?>
                        </h1>
                        <div class="flex items-center text-sm text-gray-500">
                            <span>By <?php echo htmlspecialchars($article['author_name']); ?></span>
                            <span class="mx-2">•</span>
                            <span><?php echo date('F d, Y', strtotime($article['created_at'])); ?></span>
                            <span class="mx-2">•</span>
                            <span><?php echo number_format($article['views']); ?> views</span>
                        </div>
                    </header>

                    <!-- Featured Image -->
                    <?php if ($article['image']): ?>
                        <div class="w-full h-[400px]">
                            <img src="/MovieHub/movie-website/public/assets/images/news/<?php echo htmlspecialchars($article['image']); ?>"
                                 alt="<?php echo htmlspecialchars($article['title']); ?>"
                                 class="w-full h-full object-cover">
                        </div>
                    <?php endif; ?>

                    <!-- Article Content -->
                    <div class="p-8 prose max-w-none">
                        <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                    </div>

                    <!-- Social Share -->
                    <div class="border-t border-gray-200 p-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Share this article</h3>
                        <div class="flex space-x-4">
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($article['title']); ?>"
                               target="_blank"
                               class="inline-flex items-center bg-[#1DA1F2] text-white px-4 py-2 rounded hover:bg-opacity-90">
                                <i class="fab fa-twitter mr-2"></i> Share on Twitter
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>"
                               target="_blank"
                               class="inline-flex items-center bg-[#4267B2] text-white px-4 py-2 rounded hover:bg-opacity-90">
                                <i class="fab fa-facebook mr-2"></i> Share on Facebook
                            </a>
                        </div>
                    </div>
                </article>

                <!-- Related Articles -->
                <?php if (!empty($relatedNews)): ?>
                    <div class="mt-12">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Articles</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <?php foreach ($relatedNews as $related): ?>
                                <a href="/MovieHub/movie-website/public/news/article?id=<?php echo $related['id']; ?>"
                                   class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                                    <?php if ($related['image']): ?>
                                        <img src="/MovieHub/movie-website/public/assets/images/news/<?php echo htmlspecialchars($related['image']); ?>"
                                             alt="<?php echo htmlspecialchars($related['title']); ?>"
                                             class="w-full h-48 object-cover">
                                    <?php endif; ?>
                                    <div class="p-4">
                                        <h3 class="font-semibold text-gray-900 mb-2">
                                            <?php echo htmlspecialchars($related['title']); ?>
                                        </h3>
                                        <span class="text-sm text-gray-500">
                                            <?php echo date('M d, Y', strtotime($related['created_at'])); ?>
                                        </span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>