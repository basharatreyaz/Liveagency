<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['admin_logged_in'])) {
    cms_redirect('login.php');
}
require_admin();

$page_title = 'Add Team Member';
$page_alert = '';

$name = '';
$title = '';
$experience = '';
$image = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $image = trim($_POST['image'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $title === '') {
        $page_alert = 'Please provide at least a name and title for the team member.';
    } else {
        try {
            $pdo = get_pdo();
            
            // Check if the provided email is already registered as a login user
            if ($email !== '') {
                if ($password === '') {
                    throw new Exception('You must set a password when assigning a login email.');
                }
                $check = $pdo->prepare('SELECT id FROM users WHERE LOWER(username) = LOWER(?)');
                $check->execute([$email]);
                if ($check->fetch()) {
                    throw new Exception('This email is already registered for login access.');
                }
            }

                $stmt = $pdo->prepare('INSERT INTO team_members (name, title, experience, image, email) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$name, $title, $experience, $image, $email]);

            // Automatically register them as a content Author as well
                $stmt_author = $pdo->prepare('INSERT OR IGNORE INTO authors (name, email) VALUES (?, ?)');
                $stmt_author->execute([$name, $email]);

            // Create user login credentials if email and password were provided
            if ($email !== '' && $password !== '') {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $stmt_user = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
                $stmt_user->execute([$email, $hashed_password]);
            }

            $_SESSION['admin_message'] = 'Team member added successfully.';
            cms_redirect('admin-dashboard.php');
        } catch (Exception $e) {
            $page_alert = 'Unable to save team member: ' . html_escape($e->getMessage());
        }
    }
}

include __DIR__ . '/includes/admin-header.php';
?>
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
        <div>
            <h2 style="margin:0; font-size:1.2rem; font-weight:700;">Add Team Member</h2>
            <p style="margin:.5rem 0 0; color:#475569;">Add a teammate (name, title, experience, image URL).</p>
        </div>
        <a href="admin-dashboard.php" class="btn btn-secondary">Back</a>
    </div>

    <?php if ($page_alert): ?>
        <div class="alert alert-error" style="margin-top:1rem;"><?php echo html_escape($page_alert); ?></div>
    <?php endif; ?>

    <form method="post" style="margin-top:1rem; display:grid; gap:1rem;">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="<?php echo html_escape($name); ?>" required>
        </div>

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" value="<?php echo html_escape($title); ?>" required>
        </div>

        <div class="form-group">
            <label for="experience">Experience (years or label)</label>
            <input type="text" id="experience" name="experience" value="<?php echo html_escape($experience); ?>" placeholder="e.g. 8 years">
        </div>

        <div class="form-group">
            <label for="email">Login Username / Email (Optional)</label>
            <input type="text" id="email" name="email" value="<?php echo html_escape($email); ?>" placeholder="user@example.com">
        </div>

        <div class="form-group">
            <label for="password">Login Password (Optional)</label>
            <input type="password" id="password" name="password" placeholder="Set a password for login access">
        </div>

        <div class="form-group">
            <label for="image">Image URL</label>
            <input type="url" id="image" name="image" value="<?php echo html_escape($image); ?>" placeholder="https://example.com/photo.jpg">
        </div>

        <div style="display:flex; gap:1rem;">
            <button type="submit" class="btn btn-primary">Add Member</button>
            <a href="admin-dashboard.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
