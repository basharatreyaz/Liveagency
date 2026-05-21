<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_logged_in'])) cms_redirect('login.php');
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) cms_redirect('team-list.php');

$pdo = get_pdo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $del = $pdo->prepare('DELETE FROM team_members WHERE id = ?');
        $del->execute([$id]);
        $_SESSION['admin_message'] = 'Team member deleted.';
    } catch (Exception $e) {
        $_SESSION['admin_message'] = 'Delete failed: ' . $e->getMessage();
    }
    cms_redirect('team-list.php');
}

$stmt = $pdo->prepare('SELECT id, name FROM team_members WHERE id = ?');
$stmt->execute([$id]);
$member = $stmt->fetch();
if (!$member) cms_redirect('team-list.php');

include __DIR__ . '/includes/admin-header.php';
?>
<div class="card">
    <h2>Delete Team Member</h2>
    <p>Are you sure you want to delete <strong><?php echo html_escape($member['name']); ?></strong>?</p>
    <form method="post">
        <button class="btn btn-danger" type="submit">Yes, delete</button>
        <a class="btn btn-secondary" href="team-list.php">Cancel</a>
    </form>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
