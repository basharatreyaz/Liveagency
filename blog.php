<?php 
// 1. Shared site configuration and database constants
require_once 'config.php';

// 2. Include your modular header configurations
require_once 'includes/header.php'; 

// 3. Establish connection to the local SQLite file container
$db_file = DB_FILE;
$posts = [];
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';

$posts_per_page = 8;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;
$offset = ($current_page - 1) * $posts_per_page;
$total_posts = 0;
$total_pages = 1;

if (file_exists($db_file)) {
    try {
        $pdo = new PDO("sqlite:" . $db_file);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        if ($search_query !== '') {
            $stmt_count = $pdo->prepare('SELECT COUNT(*) FROM posts WHERE status = "published" AND (title LIKE :q OR excerpt LIKE :q OR category LIKE :q)');
            $stmt_count->execute(['q' => "%$search_query%"]);
            $total_posts = $stmt_count->fetchColumn();
            
            // Filter posts matching the search query in title, excerpt, or category
            $stmt = $pdo->prepare('SELECT id, title, slug, excerpt, category, author, featured_image, created_at FROM posts WHERE status = "published" AND (title LIKE :q OR excerpt LIKE :q OR category LIKE :q) ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
            $stmt->bindValue(':q', "%$search_query%", PDO::PARAM_STR);
            $stmt->bindValue(':limit', $posts_per_page, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $total_posts = $pdo->query('SELECT COUNT(*) FROM posts WHERE status = "published"')->fetchColumn();
            
            // Fetch published entries sorted by timeline
            $stmt = $pdo->prepare('SELECT id, title, slug, excerpt, category, author, featured_image, created_at FROM posts WHERE status = "published" ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
            $stmt->bindValue(':limit', $posts_per_page, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
        }
        $posts = $stmt->fetchAll();
        $total_pages = ceil($total_posts / $posts_per_page);
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
<link rel="stylesheet" href="assets/css/blog.css?v=<?php echo time(); ?>">
<!-- Blog Page Landing Hero -->
<section class="blog-hero">
    <div class="container text-center">
        <?php if ($search_query !== ''): ?>
            <span class="section-tag">Search Results</span>
            <h1 class="page-title">Results for "<?php echo html_escape($search_query); ?>"</h1>
            <p class="page-subtitle">Found <?php echo $total_posts; ?> articles matching your query.</p>
        <?php else: ?>
            <span class="section-tag">Resources & Insights</span>
            <h1 class="page-title">Agency Insights Terminal</h1>
            <p class="page-subtitle">Expert tutorials, security diagnostics, and system optimization frameworks built directly by our support engineering team.</p>
        <?php endif; ?>
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
                <?php if ($search_query !== ''): ?>
                    <p>We couldn't find any articles matching "<strong><?php echo html_escape($search_query); ?></strong>". Try different keywords or browse our categories.</p>
                <?php else: ?>
                    <p>Our support engineers are currently compiling the latest performance reports. Check back soon!</p>
                <?php endif; ?>
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
                                Read more <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php 
                    $qs = $search_query !== '' ? '&q=' . urlencode($search_query) : '';
                    ?>
                    <?php if ($current_page > 1): ?>
                        <a href="blog?page=<?php echo $current_page - 1; ?><?php echo $qs; ?>" class="page-link">&laquo; Prev</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="blog?page=<?php echo $i; ?><?php echo $qs; ?>" class="page-link <?php echo $i === $current_page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>

                    <?php if ($current_page < $total_pages): ?>
                        <a href="blog?page=<?php echo $current_page + 1; ?><?php echo $qs; ?>" class="page-link">Next &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>

    </div>
</section>

<?php 
// Include your modular footer
require_once 'includes/footer.php'; 
?>