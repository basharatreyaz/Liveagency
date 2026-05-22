<?php
require_once __DIR__ . '/../config.php';

// Dynamically define the global base path to prevent broken CSS/JS on deep URLs (like 404s)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$base_url = rtrim($protocol . '://' . $host . $script_dir, '/') . '/';

// Default SEO Meta Tags
if (!isset($page_title_meta)) {
    $page_title_meta = 'WP Site Doctors - WordPress Maintenance & Support Agency';
}
if (!isset($page_description_meta)) {
    $page_description_meta = 'WP Site Doctors provides expert WordPress maintenance, reliable support, and optimization services for your agency or business.';
}

// Fetch custom SEO meta tags from the database based on the current active page
$current_page = basename($_SERVER['SCRIPT_NAME'], '.php');
try {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT title, description FROM seo_meta WHERE page_slug = ?');
    $stmt->execute([$current_page]);
    if ($row = $stmt->fetch()) {
        if (!empty($row['title'])) $page_title_meta = $row['title'];
        if (!empty($row['description'])) $page_description_meta = $row['description'];
    }
} catch (Exception $e) {
    // Silently fallback to defaults if DB is unavailable
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <base href="<?php echo $base_url; ?>">

    <title><?php echo html_escape($page_title_meta); ?></title>
    <meta name="description" content="<?php echo html_escape($page_description_meta); ?>">
    <!-- link favion -->
    <link rel="icon" href="assets/images/favicon.png" type="image/x-icon">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Front-End Dark Mode Initialization -->
    <script>
        // Prevent FOUC for dark mode by applying it instantly before body loads
        if (localStorage.getItem('site_theme') === 'dark' || (!('site_theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark-theme');
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggleBtns = document.querySelectorAll('.theme-toggle-btn');
            const htmlEl = document.documentElement;
            
            themeToggleBtns.forEach(btn => {
                const themeIcon = btn.querySelector('i');
                if (htmlEl.classList.contains('dark-theme')) themeIcon.className = 'fa-solid fa-sun';
                
                btn.addEventListener('click', function() {
                    htmlEl.classList.toggle('dark-theme');
                    const isDark = htmlEl.classList.contains('dark-theme');
                    localStorage.setItem('site_theme', isDark ? 'dark' : 'light');
                    
                    document.querySelectorAll('.theme-toggle-btn i').forEach(icon => {
                        icon.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
                    });
                });
            });
        });
    </script>
</head>
<body>

    <!-- Main Site Header -->
    <header class="site-header">
        <div class="container header-inner">
            
            <!-- Logo Section -->
            <div class="logo">
                <a href="index.php" class="text-logo">
                    <span>WP</span> Site Doctors
                </a>
            </div>

            <!-- Mobile Controls -->
            <div class="mobile-controls">
                <button aria-label="Toggle Dark Mode" class="theme-toggle-btn" style="background:none; border:none; color:var(--heading-color); cursor:pointer; font-size:1.25rem;"><i class="fa-solid fa-moon"></i></button>
                <button class="hamburger" id="mobile-menu-btn" aria-label="Toggle navigation">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </button>
            </div>

            <!-- Primary Navigation -->
            <nav class="main-nav" id="primary-nav">
                <ul>
                    <li><a href="./">Home</a></li>
                    <li><a href="services">Services</a></li>
                    <li><a href="about">About</a></li>
                    <li><a href="pricing">Pricing</a></li>
                    <li><a href="team">Team</a></li>
                    <li><a href="blog">Blog</a></li>
                    <li><a href="contact">Contact</a></li>
                </ul>
                <!-- Move CTA into the nav for mobile screens -->
                <!-- <div class="header-cta mobile-cta">
                    <a href="quote.php" class="btn btn-primary">Get a Quote</a>
                </div> -->
            </nav>

            <!-- Desktop Call to Action -->
            <div class="header-cta desktop-cta">
                <button aria-label="Toggle Dark Mode" class="theme-toggle-btn" style="background:none; border:none; color:var(--heading-color); cursor:pointer; font-size:1.25rem;"><i class="fa-solid fa-moon"></i></button>
                <a href="quote" class="btn btn-primary">Get a Quote</a>
            </div>
            
        </div>
    </header>

    <!-- Main Content Area Starts Here -->
    <main class="site-content">
        
        <!-- Back to Top Button -->
        <button id="backToTop" class="back-to-top" aria-label="Back to top">
            <i class="fa-solid fa-arrow-up"></i>
        </button>

        <!-- Back to Top Script -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const backToTopBtn = document.getElementById('backToTop');
                if (backToTopBtn) {
                    window.addEventListener('scroll', function() {
                        if (window.scrollY > 300) {
                            backToTopBtn.classList.add('show');
                        } else {
                            backToTopBtn.classList.remove('show');
                        }
                    });
                    backToTopBtn.addEventListener('click', function() {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                }
            });
        </script>