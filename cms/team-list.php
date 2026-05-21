<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_logged_in'])) cms_redirect('login.php');
require_admin();

$page_title = 'Team Members';
$page_alert = '';

try {
    $pdo = get_pdo();
    $members = $pdo->query('SELECT id, name, title, experience, image, created_at FROM team_members ORDER BY created_at DESC')->fetchAll();
} catch (Exception $e) {
    $members = [];
    $page_alert = 'Unable to load team members: ' . html_escape($e->getMessage());
}

include __DIR__ . '/includes/admin-header.php';
?>
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <div>
            <h2 style="margin:0; font-size:1.2rem; font-weight:700;">Team Members</h2>
            <p style="margin:.5rem 0 0; color:#475569;">Manage team members.</p>
        </div>
        <div style="display:flex; gap:0.5rem;">
            <a href="add-team.php" class="btn btn-primary">Add Member</a>
            <a href="admin-dashboard.php" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <?php if ($page_alert): ?>
        <div class="alert alert-error" style="margin-top:1rem;"><?php echo html_escape($page_alert); ?></div>
    <?php endif; ?>

    <div class="table-wrapper" style="margin-top:1rem;">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Title</th>
                    <th>Experience</th>
                    <th>Added</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($members)): ?>
                    <tr><td colspan="5" style="text-align:center; padding:2rem; color:#64748b;">No team members yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($members as $m): ?>
                        <tr>
                            <td><?php echo html_escape($m['name']); ?></td>
                            <td><?php echo html_escape($m['title']); ?></td>
                            <td><?php echo html_escape($m['experience']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($m['created_at'])); ?></td>
                            <td class="action-links">
                                <a href="edit-team.php?id=<?php echo (int)$m['id']; ?>">Edit</a>
                                <a href="delete-team.php?id=<?php echo (int)$m['id']; ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
