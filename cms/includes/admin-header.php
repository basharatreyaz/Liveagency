﻿<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Dynamically define the global base path to prevent broken CSS/JS on clean URLs
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$base_url = rtrim($protocol . '://' . $host . $script_dir, '/') . '/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo $base_url; ?>">
    <title><?php echo isset($page_title) ? html_escape($page_title) : 'CMS Administration'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Outfit:400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css?v=<?php echo time(); ?>">
    <script>
        // Prevent FOUC for dark mode by applying it instantly before body loads
        if (localStorage.getItem('cms_theme') === 'dark' || (!('cms_theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark-theme');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('admin-menu-toggle');
            const overlay = document.getElementById('admin-sidebar-overlay');
            if (toggleBtn && overlay) {
                toggleBtn.addEventListener('click', function() {
                    document.body.classList.add('sidebar-open');
                });
                overlay.addEventListener('click', function() {
                    document.body.classList.remove('sidebar-open');
                });
            }

            // Dark Mode Toggle Logic
            const themeToggleBtn = document.getElementById('theme-toggle-btn');
            const themeIcon = themeToggleBtn.querySelector('i');
            const htmlEl = document.documentElement;

            if (htmlEl.classList.contains('dark-theme')) {
                themeIcon.className = 'fa-solid fa-sun';
            }

            themeToggleBtn.addEventListener('click', function() {
                htmlEl.classList.toggle('dark-theme');
                const isDark = htmlEl.classList.contains('dark-theme');
                localStorage.setItem('cms_theme', isDark ? 'dark' : 'light');
                themeIcon.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
            });
        });
    </script>
</head>
<body>
<div id="admin-sidebar-overlay" class="admin-sidebar-overlay"></div>
<div class="admin-shell">
    <aside class="admin-sidebar">
        <a class="brand" href="admin-dashboard.php"><i class="fa-solid fa-hospital-symbol"></i> WP Site Doctors CMS</a>
        <nav class="admin-nav">
            <a href="admin-dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="add-post.php"><i class="fa-solid fa-feather-pointed"></i> Add Post</a>
            <?php if (function_exists('is_admin_user') && is_admin_user()): ?>
                <a href="categories.php"><i class="fa-solid fa-tags"></i> Categories</a>
                <a href="authors.php"><i class="fa-solid fa-user-pen"></i> Authors</a>
                <a href="team-list.php"><i class="fa-solid fa-users"></i> Team Members</a>
                <a href="seo-manager.php"><i class="fa-solid fa-magnifying-glass-chart"></i> SEO Manager</a>
                <a href="backups.php"><i class="fa-solid fa-database"></i> Backups</a>
            <?php endif; ?>
            <a href="../blog"><i class="fa-solid fa-newspaper"></i> View Blog</a>
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </nav>
    </aside>
    <div class="admin-main">
        <header class="admin-topbar">
            <div style="display: flex; align-items: center;">
                <button id="admin-menu-toggle" class="admin-menu-toggle" aria-label="Toggle menu"><i class="fa-solid fa-bars"></i></button>
                <h1 class="page-title"><?php echo isset($page_title) ? html_escape($page_title) : 'CMS Administration'; ?></h1>
            </div>
            <div class="admin-tools">
                <button id="theme-toggle-btn" aria-label="Toggle Dark Mode" style="background:none; border:none; color:inherit; cursor:pointer; font-size:1.1rem; margin-right:0.75rem;"><i class="fa-solid fa-moon"></i></button>
                <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> <span class="hide-mobile">Logout</span></a>
            </div>
        </header>
        <main class="admin-content">
            <?php if (!empty($page_alert)): ?>
                <div class="alert"><?php echo $page_alert; ?></div>
            <?php endif; ?>
