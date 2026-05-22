<?php
require_once __DIR__ . '/../config.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}
require_admin();

$page_alert = '';
$edit_author = null;
$authors = [];

try {
    $pdo = get_pdo();

    // Dynamically verify if current user is the root admin by checking the lowest ID
    $stmt_root = $pdo->query('SELECT id FROM users ORDER BY id ASC LIMIT 1');
    $root_id = $stmt_root->fetchColumn();
    $is_admin = (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] == $root_id);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_csrf();

        $action = $_POST['action'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($action === 'add' || $action === 'edit') {
            if ($name === '') {
                $page_alert = 'Author name cannot be empty.';
            } else {
                if ($action === 'add') {
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

                    $stmt = $pdo->prepare('INSERT INTO authors (name, email) VALUES (?, ?)');
                    $stmt->execute([$name, $email]);

                    // Create user login credentials if email and password were provided
                    if ($email !== '' && $password !== '') {
                        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                        $stmt_user = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
                        $stmt_user->execute([$email, $hashed_password]);
                    }
                    cms_redirect('authors.php');
                }

                if ($action === 'edit') {
                    $id = intval($_POST['id'] ?? 0);
                    
                    if ($is_admin) {
                        $stmt = $pdo->prepare('SELECT email FROM authors WHERE id = ?');
                        $stmt->execute([$id]);
                        $old_email = $stmt->fetchColumn();

                        // Prevent creating a phantom record if no password given on a new email assignment
                        if (!$old_email && $email !== '' && $password === '') {
                            throw new Exception('You must set a password when creating a new login account.');
                        }

                        // Check if email conflicts with another user
                        if ($email !== '' && strtolower($email) !== strtolower((string)$old_email)) {
                            $check = $pdo->prepare('SELECT id FROM users WHERE LOWER(username) = LOWER(?)');
                            $check->execute([$email]);
                            if ($check->fetch()) {
                                throw new Exception('This email is already registered to another user.');
                            }
                        }

                        $stmt = $pdo->prepare('UPDATE authors SET name = ?, email = ? WHERE id = ?');
                        $stmt->execute([$name, $email, $id]);

                        if ($old_email && $email !== '' && strtolower($email) !== strtolower((string)$old_email)) {
                            $stmt_u = $pdo->prepare('UPDATE users SET username = ? WHERE LOWER(username) = LOWER(?)');
                            $stmt_u->execute([$email, $old_email]);
                        }

                        if ($email !== '' && $password !== '') {
                            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                            $check = $pdo->prepare('SELECT id FROM users WHERE LOWER(username) = LOWER(?)');
                            $check->execute([$email]);
                            if ($check->fetch()) {
                                $stmt_u = $pdo->prepare('UPDATE users SET password = ? WHERE LOWER(username) = LOWER(?)');
                                $stmt_u->execute([$hashed_password, $email]);
                            } else {
                                $stmt_u = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
                                $stmt_u->execute([$email, $hashed_password]);
                            }
                        }
                    } else {
                        $stmt = $pdo->prepare('UPDATE authors SET name = ? WHERE id = ?');
                        $stmt->execute([$name, $id]);
                    }
                    cms_redirect('authors.php');
                }
            }
        }

        if ($action === 'delete') {
            $id = intval($_POST['id'] ?? 0);
            
            if ($is_admin) {
                $stmt = $pdo->prepare('SELECT email FROM authors WHERE id = ?');
                $stmt->execute([$id]);
                $del_email = $stmt->fetchColumn();
                if ($del_email) {
                    $stmt_u = $pdo->prepare('DELETE FROM users WHERE LOWER(username) = LOWER(?)');
                    $stmt_u->execute([$del_email]);
                }
            }

            $stmt = $pdo->prepare('DELETE FROM authors WHERE id = ?');
            $stmt->execute([$id]);
            cms_redirect('authors.php');
        }
    }

    if (isset($_GET['edit'])) {
        $id = intval($_GET['edit']);
        $stmt = $pdo->prepare('SELECT id, name, email FROM authors WHERE id = ?');
        $stmt->execute([$id]);
        $edit_author = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $stmt = $pdo->query('SELECT id, name, email FROM authors ORDER BY name ASC');
    $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $page_alert = 'Error: ' . html_escape($e->getMessage());
}
?>

<?php $page_title = 'Author Management'; require_once __DIR__ . '/includes/admin-header.php'; ?>

    <section class="admin-panel">
        <h1>Author Management</h1>
        <?php if ($page_alert): ?>
            <div class="alert alert-error"><?php echo html_escape($page_alert); ?></div>
        <?php endif; ?>

        <div class="admin-grid">
            <div class="admin-card">
                <h2><?php echo $edit_author ? 'Edit Author' : 'Add Author'; ?></h2>
                <form method="post" action="authors.php">
                    <input type="hidden" name="csrf_token" value="<?php echo html_escape(get_csrf_token()); ?>">
                    <input type="hidden" name="action" value="<?php echo $edit_author ? 'edit' : 'add'; ?>">
                    <?php if ($edit_author): ?>
                        <input type="hidden" name="id" value="<?php echo (int)$edit_author['id']; ?>">
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="name">Author Name</label>
                        <input id="name" name="name" type="text" value="<?php echo html_escape($edit_author['name'] ?? ''); ?>" placeholder="e.g. Jane Doe" required>
                    </div>
                    <?php if (!$edit_author || $is_admin): ?>
                    <div class="form-group">
                        <label for="email">Login Username / Email (Optional)</label>
                        <input type="text" id="email" name="email" value="<?php echo html_escape($edit_author['email'] ?? ''); ?>" placeholder="author@example.com">
                    </div>
                    <div class="form-group">
                        <label for="password">Login Password (Optional)</label>
                        <input type="password" id="password" name="password" placeholder="<?php echo $edit_author ? 'Leave blank to keep current' : 'Set a password for login access'; ?>">
                    </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary"><?php echo $edit_author ? 'Save Changes' : 'Add Author'; ?></button>
                    <?php if ($edit_author): ?>
                        <a href="authors.php" class="btn btn-secondary">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="admin-card">
                <h2>Existing Authors</h2>
                <?php if (count($authors) === 0): ?>
                    <p>No authors found yet.</p>
                <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($authors as $author): ?>
                                <tr>
                                    <td><?php echo html_escape($author['name']); ?></td>
                                    <td>
                                        <a class="btn btn-sm" href="authors.php?edit=<?php echo (int)$author['id']; ?>">Edit</a>
                                        <form method="post" action="authors.php" style="display:inline-block; margin:0;">
                                            <input type="hidden" name="csrf_token" value="<?php echo html_escape(get_csrf_token()); ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo (int)$author['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this author?');">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </section>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
