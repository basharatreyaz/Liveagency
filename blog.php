<?php 
// 1. Shared site configuration and database constants
require_once 'config.php';

// 2. Include your modular header configurations
require_once 'includes/header.php'; 

// 3. Establish connection to the local SQLite file container
$db_file = DB_FILE;
$posts = [];

if (file_exists($db_file)) {
    try {
        $pdo = new PDO("sqlite:" . $db_file);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Fetch published entries sorted by timeline
        $stmt = $pdo->query('SELECT id, title, slug, excerpt, category, author, featured_image, created_at FROM posts WHERE status = "published" ORDER BY created_at DESC');
        $posts = $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "<section class='blog-section'><div class='container'><p class='alert alert-error'>SQLite Execution Error: " . htmlspecialchars($e->getMessage()) . "</p></div></section>";
        require_once 'includes/footer.php';
        exit;
    }
} else {
    echo "<section class='blog-section'><div class='container'><p class='alert alert-error'>Database file missing. Please execute the database configuration setup script first.</p></div></section>";
    require_once 'includes/footer.php';
    exit;
}
?>
<link rel="stylesheet" href="assets/css/blog.css">
<!-- Blog Page Landing Hero -->
<section class="blog-hero">
    <div class="container text-center">
        <span class="section-tag">Resources & Insights</span>
        <h1 class="page-title">Agency Insights Terminal</h1>
        <p class="page-subtitle">Expert tutorials, security diagnostics, and system optimization frameworks built directly by our support engineering team.</p>
    </div>
</section>

<!-- Active Posts Display Layout Grid -->
<section class="blog-section">
    <div class="container">
        
        <?php if (empty($posts)): ?>
            <!-- Fallback Empty State Display Interface -->
            <div class="blog-empty-state">
                <i class="fa-regular fa-folder-open"></i>
                <h3>No Articles Found</h3>
                <p>Our support engineers are currently compiling the latest performance reports. Check back soon!</p>
            </div>
        <?php else: ?>
            
            <div class="blog-grid">
                <?php foreach ($posts as $post): ?>
                    <!-- Compiled Dynamic Post Card Component -->
                    <article class="blog-card">
                        <?php if (!empty($post['featured_image'])): ?>
                        <div class="blog-card-image">
                            <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                        </div>
                        <?php endif; ?>
                        <div class="blog-content">
                            <span class="blog-meta-tag"><?php echo htmlspecialchars($post['category']); ?></span>
                            <h2 class="blog-title">
                                <a href="<?php echo htmlspecialchars($post['slug']); ?>">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </h2>
                            <p>
                                <?php echo htmlspecialchars($post['excerpt']); ?>
                            </p>
                            <div class="blog-details">
                                <span><i class="fa-regular fa-calendar"></i> <?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                                <span><i class="fa-regular fa-user"></i> <?php echo htmlspecialchars($post['author']); ?></span>
                            </div>
                            <a href="<?php echo htmlspecialchars($post['slug']); ?>" class="blog-readmore">
                                Read Full Report <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            
        <?php endif; ?>

    </div>
</section>

<?php 
// Include your modular footer
require_once 'includes/footer.php'; 
?>