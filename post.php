<?php
require_once 'config.php';

$db_file = DB_FILE;
$post = null;
$related_posts = [];
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if (file_exists($db_file) && !empty($slug)) {
    try {
        $pdo = get_pdo();

        // Query entry data point securely via prepared parameter bindings
        $stmt = $pdo->prepare('SELECT title, content, category, author, featured_image, created_at, excerpt, meta_title, meta_description FROM posts WHERE slug = ? AND status = "published"');
        $stmt->execute([$slug]);
        $post = $stmt->fetch();

        // Fetch related posts from the same category
        if ($post) {
            $related_stmt = $pdo->prepare('SELECT title, slug, excerpt, category, author, featured_image, created_at FROM posts WHERE category = ? AND slug != ? AND status = "published" ORDER BY created_at DESC LIMIT 3');
            $related_stmt->execute([$post['category'], $slug]);
            $related_posts = $related_stmt->fetchAll();
        }
    } catch (PDOException $e) {
        // Silently capture errors or pass them elegantly 
    }
}

// Graceful 404 configuration if database fails or returns empty variables
if (!$post) {
    // Load the global 404 layout directly
    include '404.php';
    exit;
}

// Set custom SEO tags for the post, falling back to title/excerpt if empty
$page_title_meta = !empty($post['meta_title']) ? $post['meta_title'] : $post['title'];
$page_description_meta = !empty($post['meta_description']) ? $post['meta_description'] : $post['excerpt'];

require_once 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/blog.css">

<!-- Dedicated Single Post Layout Container -->
<main class="single-post-wrapper">
    <article class="container article-container">
        
        <!-- Header Section Meta Tracks -->
        <header class="article-header">
            <div class="article-meta-tags">
                <span class="section-tag mb-0" style="text-transform: none;"><?php echo htmlspecialchars(ucwords(strtolower($post['category']))); ?></span>
                <span class="section-tag mb-0" style="text-transform: none;"><i class="fa-regular fa-user meta-icon"></i> By <?php echo htmlspecialchars(ucwords(strtolower($post['author']))); ?></span>
            </div>
            <h1 class="article-title">
                <?php echo htmlspecialchars($post['title']); ?>
            </h1>
            <div class="article-date-meta">
                <span><i class="fa-regular fa-calendar meta-icon-blue"></i> Published <?php echo date('F d, Y', strtotime($post['created_at'])); ?></span>
            </div>
        </header>

        <?php if (!empty($post['featured_image'])): ?>
        <figure class="article-featured-image">
            <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
        </figure>
        <?php endif; ?>

        <!-- Main Long-Form Article Body Renderer -->
        <div class="article-body-content">
            <?php 
            echo $post['content']; 
            ?>
        </div>

        <!-- Footer Structural Return Path Link -->
        <footer class="article-footer">
            <a href="blog.php" class="back-link">
                <i class="fa-solid fa-arrow-left"></i> Back to Article Feed Directory
            </a>
        </footer>

    </article>

    <?php if (!empty($related_posts)): ?>
    <div class="container related-articles-container">
        <h2 class="related-title">Related Articles</h2>
        <div class="blog-grid">
            <?php foreach ($related_posts as $r_post): ?>
                <article class="blog-card">
                    <?php if (!empty($r_post['featured_image'])): ?>
                    <img src="<?php echo htmlspecialchars($r_post['featured_image']); ?>" alt="<?php echo htmlspecialchars($r_post['title']); ?>" class="related-img">
                    <?php endif; ?>
                    <div class="blog-content <?php echo empty($r_post['featured_image']) ? 'pt-10' : 'pt-8'; ?>">
                        <span class="section-tag related-tag" style="text-transform: none;"><?php echo htmlspecialchars(ucwords(strtolower($r_post['category']))); ?></span>
                        <h3 class="blog-title">
                            <a href="<?php echo htmlspecialchars($r_post['slug']); ?>">
                                <?php echo htmlspecialchars($r_post['title']); ?>
                            </a>
                        </h3>
                        <p>
                            <?php echo htmlspecialchars($r_post['excerpt']); ?>
                        </p>
                        <div class="blog-details">
                            <span><i class="fa-regular fa-calendar meta-icon"></i> <?php echo date('M d, Y', strtotime($r_post['created_at'])); ?></span>
                        </div>
                        <a href="<?php echo htmlspecialchars($r_post['slug']); ?>" class="blog-readmore">
                            Read more <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</main>

<?php 
require_once 'includes/footer.php'; 
?>