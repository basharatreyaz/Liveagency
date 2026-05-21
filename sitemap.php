<?php
// Start output buffering to catch any invisible Byte Order Marks (BOM) or whitespace
ob_start();

// Initialize database connection
require_once 'config.php';

// Set header to output XML
header("Content-Type: text/xml;charset=utf-8");

// Determine base URL dynamically
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$base_url = rtrim($protocol . '://' . $host . $script_dir, '/') . '/';

// Define static pages and their priority/frequency
$static_pages = [
    '' => ['priority' => '1.0', 'changefreq' => 'daily'],
    'about' => ['priority' => '0.8', 'changefreq' => 'monthly'],
    'services' => ['priority' => '0.9', 'changefreq' => 'weekly'],
    'pricing' => ['priority' => '0.9', 'changefreq' => 'weekly'],
    'team' => ['priority' => '0.7', 'changefreq' => 'monthly'],
    'blog' => ['priority' => '0.9', 'changefreq' => 'daily'],
    'contact' => ['priority' => '0.8', 'changefreq' => 'monthly'],
    'quote' => ['priority' => '0.8', 'changefreq' => 'monthly'],
    'audit' => ['priority' => '0.8', 'changefreq' => 'monthly'],
    'terms' => ['priority' => '0.5', 'changefreq' => 'yearly'],
    'privacy' => ['priority' => '0.5', 'changefreq' => 'yearly'],
    'refund' => ['priority' => '0.5', 'changefreq' => 'yearly'],
];

// Clean the output buffer to wipe any accidental whitespace/BOM from included files
if (ob_get_length()) {
    ob_clean();
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Add static pages
$current_date = date('Y-m-d');
foreach ($static_pages as $slug => $meta) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($base_url . $slug) . "</loc>\n";
    echo "    <lastmod>{$current_date}</lastmod>\n";
    echo "    <changefreq>{$meta['changefreq']}</changefreq>\n";
    echo "    <priority>{$meta['priority']}</priority>\n";
    echo "  </url>\n";
}

// Fetch and add dynamic blog posts
try {
    $pdo = get_pdo();
    $stmt = $pdo->query("SELECT slug, created_at FROM posts WHERE status = 'published' ORDER BY created_at DESC");
    
    while ($post = $stmt->fetch()) {
        $post_date = date('Y-m-d', strtotime($post['created_at']));
        echo "  <url>\n";
        echo "    <loc>" . htmlspecialchars($base_url . $post['slug']) . "</loc>\n";
        echo "    <lastmod>{$post_date}</lastmod>\n";
        echo "    <changefreq>monthly</changefreq>\n";
        echo "    <priority>0.7</priority>\n";
        echo "  </url>\n";
    }
} catch (Exception $e) {
    // Silently continue if database fails
}

echo "</urlset>";
?>