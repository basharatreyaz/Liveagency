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
$details = '';
$linkedin = '';
$instagram = '';
$facebook = '';
$display_order = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $name = trim($_POST['name'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $experience = trim($_POST['experience'] ?? '');
    $image = trim($_POST['image'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $details = trim($_POST['details'] ?? '');
    $linkedin = trim($_POST['linkedin'] ?? '');
    $instagram = trim($_POST['instagram'] ?? '');
    $facebook = trim($_POST['facebook'] ?? '');
    $display_order = (int)($_POST['display_order'] ?? 0);

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

                $stmt = $pdo->prepare('INSERT INTO team_members (name, title, experience, image, email, details, linkedin, instagram, facebook, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$name, $title, $experience, $image, $email, $details, $linkedin, $instagram, $facebook, $display_order]);

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
        <input type="hidden" name="csrf_token" value="<?php echo html_escape(get_csrf_token()); ?>">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="<?php echo html_escape($name); ?>" required>
        </div>

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" value="<?php echo html_escape($title); ?>" required>
        </div>

        <div class="form-group">
            <label for="display_order">Display Order</label>
            <input type="number" id="display_order" name="display_order" value="<?php echo html_escape((string)$display_order); ?>" placeholder="0 (Lower numbers appear first)">
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

        <div class="form-group">
            <label for="details">Author Details / Bio</label>
            <textarea id="details" name="details" rows="4" placeholder="Enter background details, skills, or biography..."><?php echo html_escape($details); ?></textarea>
        </div>

        <div class="form-group">
            <label for="linkedin">LinkedIn URL (Optional)</label>
            <input type="url" id="linkedin" name="linkedin" value="<?php echo html_escape($linkedin); ?>" placeholder="https://linkedin.com/in/username">
        </div>

        <div class="form-group">
            <label for="instagram">Instagram URL (Optional)</label>
            <input type="url" id="instagram" name="instagram" value="<?php echo html_escape($instagram); ?>" placeholder="https://instagram.com/username">
        </div>

        <div class="form-group">
            <label for="facebook">Facebook URL (Optional)</label>
            <input type="url" id="facebook" name="facebook" value="<?php echo html_escape($facebook); ?>" placeholder="https://facebook.com/username">
        </div>

        <div style="display:flex; gap:1rem;">
            <button type="submit" class="btn btn-primary">Add Member</button>
            <a href="admin-dashboard.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
