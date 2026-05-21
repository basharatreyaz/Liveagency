﻿﻿﻿<?php
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

try {
    $pdo = get_pdo();
    $posts = $pdo->query('SELECT id, title, slug, category, author, status, created_at FROM posts ORDER BY created_at DESC')->fetchAll();
} catch (Exception $exception) {
    $page_alert = '<strong>Database Error:</strong> ' . html_escape($exception->getMessage());
    $posts = [];
}

include __DIR__ . '/includes/admin-header.php';
?>
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
</div>
<?php include __DIR__ . '/includes/admin-footer.php'; ?>
