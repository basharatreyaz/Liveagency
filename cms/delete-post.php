﻿<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['admin_logged_in'])) {
    cms_redirect('login.php');
}
require_admin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$page_title = 'Delete Post';
$page_alert = '';

try {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT id, title FROM posts WHERE id = ?');
    $stmt->execute([$id]);
    $post = $stmt->fetch();

    if (!$post) {
        throw new Exception('Post not found.');
    }
} catch (Exception $exception) {
    $_SESSION['admin_message'] = 'Unable to find the requested post.';
    cms_redirect('admin-dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare('DELETE FROM posts WHERE id = ?');
        $stmt->execute([$id]);
        $_SESSION['admin_message'] = 'Post deleted successfully.';
        cms_redirect('admin-dashboard.php');
    } catch (Exception $exception) {
        $page_alert = 'Unable to delete the post: ' . html_escape($exception->getMessage());
    }
}

include __DIR__ . '/includes/admin-header.php';
?>
<div class="card">
    <h2 style="margin-top:0; font-size:1.25rem;">Confirm Delete</h2>
    <p style="color:#475569;">You are about to permanently remove the post titled:</p>
    <p style="font-weight:700; color:#111827; margin:1rem 0;"><?php echo html_escape($post['title']); ?></p>
    <form method="post">
        <div style="display:flex; gap:1rem; flex-wrap:wrap; margin-top:1rem;">
            <button type="submit" class="btn btn-primary">Delete Post</button>
            <a href="admin-dashboard.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php include __DIR__ . '/includes/admin-footer.php'; ?>
