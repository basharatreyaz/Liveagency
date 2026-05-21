<?php
require_once __DIR__ . '/../config.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}
require_admin();

$page_alert = '';
$edit_category = null;

try {
    $pdo = get_pdo();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        $name = trim($_POST['name'] ?? '');

        if ($action === 'add' || $action === 'edit') {
            if ($name === '') {
                $page_alert = 'Category name cannot be empty.';
            } else {
                if ($action === 'add') {
                    $stmt = $pdo->prepare('INSERT INTO categories (name) VALUES (?)');
                    $stmt->execute([$name]);
                    cms_redirect('categories.php');
                }

                if ($action === 'edit') {
                    $id = intval($_POST['id'] ?? 0);
                    $stmt = $pdo->prepare('UPDATE categories SET name = ? WHERE id = ?');
                    $stmt->execute([$name, $id]);
                    cms_redirect('categories.php');
                }
            }
        }

        if ($action === 'delete') {
            $id = intval($_POST['id'] ?? 0);
            $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
            $stmt->execute([$id]);
            cms_redirect('categories.php');
        }
    }

    if (isset($_GET['edit'])) {
        $id = intval($_GET['edit']);
        $stmt = $pdo->prepare('SELECT id, name FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        $edit_category = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $stmt = $pdo->query('SELECT id, name FROM categories ORDER BY name ASC');
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $page_alert = 'Unable to load categories: ' . html_escape($e->getMessage());
}
?>

<?php $page_title = 'Category Management'; require_once __DIR__ . '/includes/admin-header.php'; ?>

    <section class="admin-panel">
        <h1>Category Management</h1>
        <?php if ($page_alert): ?>
            <div class="alert alert-error"><?php echo html_escape($page_alert); ?></div>
        <?php endif; ?>

        <div class="admin-grid">
            <div class="admin-card">
                <h2><?php echo $edit_category ? 'Edit Category' : 'Add Category'; ?></h2>
                <form method="post" action="categories.php">
                    <input type="hidden" name="action" value="<?php echo $edit_category ? 'edit' : 'add'; ?>">
                    <?php if ($edit_category): ?>
                        <input type="hidden" name="id" value="<?php echo (int)$edit_category['id']; ?>">
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="name">Category Name</label>
                        <input id="name" name="name" type="text" value="<?php echo html_escape($edit_category['name'] ?? ''); ?>" placeholder="e.g. Security" required>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo $edit_category ? 'Save Changes' : 'Add Category'; ?></button>
                    <?php if ($edit_category): ?>
                        <a href="categories.php" class="btn btn-secondary">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="admin-card">
                <h2>Existing Categories</h2>
                <?php if (count($categories) === 0): ?>
                    <p>No categories found yet.</p>
                <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo html_escape($category['name']); ?></td>
                                    <td>
                                        <a class="btn btn-sm" href="categories.php?edit=<?php echo (int)$category['id']; ?>">Edit</a>
                                        <form method="post" action="categories.php" style="display:inline-block; margin:0;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo (int)$category['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?');">Delete</button>
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
