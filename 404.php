<?php 
// Set explicit 404 header for search engines
http_response_code(404);

// Include your modular header
require_once 'includes/header.php'; 
?>

<section style="padding: 10rem 2rem; text-align: center; min-height: 65vh; display: flex; align-items: center; justify-content: center; background-color: var(--bg-light);">
    <div class="container">
        <i class="fa-solid fa-route" style="font-size: 5rem; color: var(--primary); margin-bottom: 2rem;"></i>
        <h1 class="page-title">404 - Page Not Found</h1>
        <p class="page-subtitle" style="margin-bottom: 3rem;">The URL you requested was not found on this server. It might have been archived, relocated, or deleted.</p>
        <a href="index.php" class="btn btn-primary">Return to Homepage <i class="fa-solid fa-arrow-right" style="margin-left: 8px; font-size: 14px;"></i></a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>