﻿<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['admin_logged_in'])) {
    cms_redirect('login.php');
}


$page_title = 'Dashboard';
$page_alert = '';
if (!empty($_SESSION['admin_message'])) {
    $page_alert = html_escape($_SESSION['admin_message']);
    unset($_SESSION['admin_message']);
}

$posts_per_page = 15;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;
$offset = ($current_page - 1) * $posts_per_page;
$total_posts = 0;
$total_pages = 1;

try {
    $pdo = get_pdo();
    $total_posts = $pdo->query('SELECT COUNT(*) FROM posts')->fetchColumn();
    $total_pages = ceil($total_posts / $posts_per_page);
    
    $stmt = $pdo->prepare('SELECT id, title, slug, category, author, status, created_at FROM posts ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
    $stmt->bindValue(':limit', $posts_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $posts = $stmt->fetchAll();
} catch (Exception $exception) {
    $page_alert = '<strong>Database Error:</strong> ' . html_escape($exception->getMessage());
    $posts = [];
}

$backup_dir = __DIR__ . '/data/backups';

include __DIR__ . '/includes/admin-header.php';
?>

<div style="margin-bottom: 2rem; display: flex; gap: 1rem; flex-wrap: wrap;">
    <div class="card" style="flex: 1; min-width: 250px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: none;">
        <div style="background: rgba(16, 185, 129, 0.15); color: #10b981; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <div>
            <h3 style="margin: 0; font-size: 1.05rem;">Database Integrity</h3>
            <p style="margin: 0.25rem 0 0; opacity: 0.8; font-size: 0.9rem;">
                Secured. <strong><?php echo is_dir($backup_dir) ? count(glob($backup_dir . '/backup_*.db')) : 0; ?></strong> manual backups retained. <br><a href="backups.php" style="color:var(--primary); font-size:0.85rem; font-weight:600; text-decoration:none;"><i class="fa-solid fa-arrow-right"></i> Manage Backups</a>
            </p>
        </div>
    </div>
    <div class="card" style="flex: 1; min-width: 250px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: none;">
        <div style="background: rgba(59, 130, 246, 0.15); color: #3b82f6; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
            <i class="fa-solid fa-newspaper"></i>
        </div>
        <div>
            <h3 style="margin: 0; font-size: 1.05rem;">Content Repository</h3>
            <p style="margin: 0.25rem 0 0; opacity: 0.8; font-size: 0.9rem;">
                <strong><?php echo $total_posts; ?></strong> total active database records.
            </p>
        </div>
    </div>
</div>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
        <div>
            <h2 style="margin:0; font-size:1.2rem; font-weight:700;">Recent Posts</h2>
            <p style="margin:.5rem 0 0; color:#475569;">Manage published articles, drafts, and editorial updates.</p>
        </div>
        <div style="display:flex; gap:0.5rem;">
            <a href="add-post.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Create New Post</a>
        </div>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Published</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($posts)): ?>
                    <tr><td colspan="6" style="text-align:center; padding:2rem; color:#64748b;">No posts available yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><?php echo html_escape($post['title']); ?></td>
                            <td><?php echo html_escape($post['category']); ?></td>
                            <td><?php echo html_escape($post['author']); ?></td>
                            <td><span class="status-pill <?php echo $post['status'] === 'published' ? 'status-published' : 'status-draft'; ?>"><?php echo html_escape(ucfirst($post['status'])); ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($post['created_at'])); ?></td>
                            <td class="action-links">
                                <a href="edit-post.php?id=<?php echo $post['id']; ?>">Edit</a>
                                <?php if (function_exists('is_admin_user') && is_admin_user()): ?>
                                    <a href="delete-post.php?id=<?php echo $post['id']; ?>">Delete</a>
                                <?php endif; ?>
                                <?php if ($post['status'] === 'published'): ?>
                                    <a href="../<?php echo urlencode($post['slug']); ?>" target="_blank">View</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>    
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="pagination" style="display:flex; justify-content:center; gap:0.5rem; margin-top:2rem; padding-bottom:1rem;">
            <?php if ($current_page > 1): ?>
                <a href="admin-dashboard.php?page=<?php echo $current_page - 1; ?>" class="btn btn-secondary" style="padding: 0.25rem 0.75rem;">&laquo; Prev</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="admin-dashboard.php?page=<?php echo $i; ?>" class="btn <?php echo $i === $current_page ? 'btn-primary' : 'btn-secondary'; ?>" style="padding: 0.25rem 0.75rem;"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages): ?>
                <a href="admin-dashboard.php?page=<?php echo $current_page + 1; ?>" class="btn btn-secondary" style="padding: 0.25rem 0.75rem;">Next &raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/admin-footer.php'; ?>
