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

<div class="min-h-screen bg-gray-900 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <a href="/MovieHub/movie-website/public/index.php?action=news_list"
                class="inline-flex items-center text-blue-500 hover:text-blue-400 mb-8">
                <i class="fas fa-arrow-left mr-2"></i> Back to News
            </a>

            <article class="bg-gray-800 rounded-lg overflow-hidden shadow-xl">
                <img src="/MovieHub/movie-website/public/assets/images/news/<?php echo htmlspecialchars($article['image']); ?>"
                    alt="<?php echo htmlspecialchars($article['title']); ?>"
                    class="w-full h-96 object-cover">

                <div class="p-8">
                    <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">
                        <?php echo htmlspecialchars($article['title']); ?>
                    </h1>

                    <div class="flex flex-wrap items-center justify-between gap-4 text-gray-500 mb-8">
                        <div class="flex items-center">
                            <i class="far fa-calendar mr-2"></i>
                            <span><?php echo date('F d, Y', strtotime($article['created_at'])); ?></span>
                        </div>

                        <!-- Social Share Buttons -->
                        <div class="flex items-center space-x-4">
                            <span class="text-sm">Share:</span>
                            <a href="<?php echo $shareFacebook; ?>" target="_blank" rel="noopener noreferrer"
                                class="text-blue-500 hover:text-blue-400 transition-colors">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="<?php echo $shareTwitter; ?>" target="_blank" rel="noopener noreferrer"
                                class="text-blue-400 hover:text-blue-300 transition-colors">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="<?php echo $shareLinkedIn; ?>" target="_blank" rel="noopener noreferrer"
                                class="text-blue-600 hover:text-blue-500 transition-colors">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>

                    <div class="prose prose-lg text-gray-300 max-w-none mb-8">
                        <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                    </div>

                    <!-- Tags or Categories could go here -->
                </div>
            </article>

            <!-- Related News Section -->
            <?php if (!empty($relatedNews)): ?>
                <div class="mt-12">
                    <h2 class="text-2xl font-bold text-white mb-6">Related News</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <?php foreach ($relatedNews as $related): ?>
                            <a href="/MovieHub/movie-website/public/index.php?action=news_detail&id=<?php echo $related['id']; ?>"
                                class="bg-gray-800 rounded-lg overflow-hidden shadow-lg hover:transform hover:scale-105 transition-transform duration-300">
                                <img src="/MovieHub/movie-website/public/assets/images/news/<?php echo htmlspecialchars($related['image']); ?>"
                                    alt="<?php echo htmlspecialchars($related['title']); ?>"
                                    class="w-full h-48 object-cover">
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold text-white mb-2">
                                        <?php echo htmlspecialchars($related['title']); ?>
                                    </h3>
                                    <p class="text-sm text-gray-400">
                                        <?php echo date('M d, Y', strtotime($related['created_at'])); ?>
                                    </p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add this before closing body tag -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle share button clicks
        const shareButtons = document.querySelectorAll('[data-share]');
        shareButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const url = button.href;
                window.open(url, 'share-dialog', 'width=626,height=436');
            });
        });
    });
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>