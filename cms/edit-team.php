<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_logged_in'])) cms_redirect('login.php');
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$page_alert = '';

if ($id <= 0) {
    cms_redirect('team-list.php');
}

try {
    $pdo = get_pdo();
    
    // Ensure email column exists
    try {
        $pdo->exec("ALTER TABLE team_members ADD COLUMN email TEXT DEFAULT ''");
    } catch (Exception $e) {}

    // Dynamically verify if current user is the root admin
    $stmt_root = $pdo->query('SELECT id FROM users ORDER BY id ASC LIMIT 1');
    $root_id = $stmt_root->fetchColumn();
    $is_admin = (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] == $root_id);

    $stmt = $pdo->prepare('SELECT id, name, title, experience, image, email FROM team_members WHERE id = ?');
    $stmt->execute([$id]);
    $member = $stmt->fetch();
} catch (Exception $e) {
    $member = null;
}

if (!$member) {
    cms_redirect('team-list.php');
}

$name = $member['name'];
$title = $member['title'];
$experience = $member['experience'];
$image = $member['image'];
$email = $member['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $image = trim($_POST['image'] ?? '');
    $new_email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $title === '') {
        $page_alert = 'Please provide at least a name and title.';
    } else {
        try {
            if ($is_admin) {
                // Check for email conflict
                if ($new_email !== '' && strtolower($new_email) !== strtolower((string)$email)) {
                    $check = $pdo->prepare('SELECT id FROM users WHERE LOWER(username) = LOWER(?)');
                    $check->execute([$new_email]);
                    if ($check->fetch()) {
                        throw new Exception('This username/email is already registered to another user.');
                    }
                }

                $update = $pdo->prepare('UPDATE team_members SET name = ?, title = ?, experience = ?, image = ?, email = ? WHERE id = ?');
                $update->execute([$name, $title, $experience, $image, $new_email, $id]);

                if ($email !== '' && $new_email !== '' && strtolower($new_email) !== strtolower((string)$email)) {
                    $stmt_u = $pdo->prepare('UPDATE users SET username = ? WHERE LOWER(username) = LOWER(?)');
                    $stmt_u->execute([$new_email, $email]);
                }

                if ($new_email !== '' && $password !== '') {
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    $check = $pdo->prepare('SELECT id FROM users WHERE LOWER(username) = LOWER(?)');
                    $check->execute([$new_email]);
                    if ($check->fetch()) {
                        $stmt_u = $pdo->prepare('UPDATE users SET password = ? WHERE LOWER(username) = LOWER(?)');
                        $stmt_u->execute([$hashed_password, $new_email]);
                    } else {
                        $stmt_u = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
                        $stmt_u->execute([$new_email, $hashed_password]);
                    }
                }
            } else {
                $update = $pdo->prepare('UPDATE team_members SET name = ?, title = ?, experience = ?, image = ? WHERE id = ?');
                $update->execute([$name, $title, $experience, $image, $id]);
            }
            $_SESSION['admin_message'] = 'Team member updated.';
            cms_redirect('team-list.php');
        } catch (Exception $e) {
            $page_alert = 'Unable to update: ' . html_escape($e->getMessage());
        }
    }
}

include __DIR__ . '/includes/admin-header.php';
?>
<div class="card">
    <h2 style="margin:0 0 1rem 0;">Edit Team Member</h2>
    <?php if ($page_alert): ?>
        <div class="alert alert-error"><?php echo html_escape($page_alert); ?></div>
    <?php endif; ?>

    <form method="post" style="display:grid; gap:1rem;">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input id="name" name="name" value="<?php echo html_escape($name); ?>" required>
        </div>
        <div class="form-group">
            <label for="title">Title</label>
            <input id="title" name="title" value="<?php echo html_escape($title); ?>" required>
        </div>
        <div class="form-group">
            <label for="experience">Experience</label>
            <input id="experience" name="experience" value="<?php echo html_escape($experience); ?>">
        </div>
        <div class="form-group">
            <label for="image">Image URL</label>
            <input id="image" name="image" value="<?php echo html_escape($image); ?>" type="url">
        </div>
        <?php if ($is_admin): ?>
        <div class="form-group">
            <label for="email">Login Username / Email</label>
            <input type="text" id="email" name="email" value="<?php echo html_escape($email); ?>">
        </div>
        <div class="form-group">
            <label for="password">Update Login Password (Optional)</label>
            <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">
        </div>
        <?php endif; ?>
        <div style="display:flex; gap:1rem;">
            <button class="btn btn-primary" type="submit">Save</button>
            <a class="btn btn-secondary" href="team-list.php">Cancel</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
