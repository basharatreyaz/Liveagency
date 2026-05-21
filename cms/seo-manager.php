<?php
require_once __DIR__ . '/../config.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}
require_admin();

$page_alert = '';
$success_msg = '';

// Define the core architecture pages mapped on the site
$pages = [
    'index' => 'Home',
    'about' => 'About Us',
    'services' => 'Services',
    'pricing' => 'Pricing',
    'team' => 'Team',
    'blog' => 'Blog',
    'contact' => 'Contact',
    'quote' => 'Quote',
    'audit' => 'Audit',
    'terms' => 'Terms & Conditions',
    'privacy' => 'Privacy Policy',
    'refund' => 'Refund Policy'
];

try {
    $pdo = get_pdo();

    // Handle incoming Form Submission Updates
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
        $slug = $_POST['page_slug'] ?? '';
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (array_key_exists($slug, $pages)) {
            // Check if record exists safely to support legacy SQLite arrays
            $stmt = $pdo->prepare('SELECT id FROM seo_meta WHERE page_slug = ?');
            $stmt->execute([$slug]);
            
            if ($stmt->fetch()) {
                $update = $pdo->prepare('UPDATE seo_meta SET title = ?, description = ? WHERE page_slug = ?');
                $update->execute([$title, $description, $slug]);
            } else {
                $insert = $pdo->prepare('INSERT INTO seo_meta (page_slug, title, description) VALUES (?, ?, ?)');
                $insert->execute([$slug, $title, $description]);
            }
            
            $success_msg = 'SEO details for ' . html_escape($pages[$slug]) . ' updated successfully.';
        }
    }

    // Fetch all current configurations
    $stmt = $pdo->query('SELECT page_slug, title, description FROM seo_meta');
    $seo_data = [];
    while ($row = $stmt->fetch()) {
        $seo_data[$row['page_slug']] = $row;
    }
} catch (Exception $e) {
    $page_alert = 'Error loading SEO details: ' . html_escape($e->getMessage());
}

$edit_slug = $_GET['edit'] ?? '';
?>

<?php $page_title = 'SEO Meta Management'; require_once __DIR__ . '/includes/admin-header.php'; ?>

<section class="admin-panel">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.5rem;">
        <h1 style="margin:0;">SEO Meta Management</h1>
    </div>
    
    <?php if ($page_alert): ?>
        <div class="alert alert-error"><?php echo html_escape($page_alert); ?></div>
    <?php endif; ?>
    <?php if ($success_msg): ?>
        <div class="alert alert-success" style="background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
            <strong><i class="fa-solid fa-circle-check" style="margin-right: 6px;"></i>Success:</strong> <?php echo html_escape($success_msg); ?>
        </div>
    <?php endif; ?>

    <div class="admin-grid" style="grid-template-columns: 1fr;">
        <?php if (array_key_exists($edit_slug, $pages)): ?>
            <div class="admin-card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.5rem;">
                    <h2 style="margin:0;">Editing: <?php echo html_escape($pages[$edit_slug]); ?></h2>
                    <a href="seo-manager.php" class="btn btn-sm btn-secondary">Cancel & Go Back</a>
                </div>
                <form method="post" action="seo-manager.php?edit=<?php echo html_escape($edit_slug); ?>">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="page_slug" value="<?php echo html_escape($edit_slug); ?>">
                    
                    <div class="form-group">
                        <label for="title">Meta Title Tag</label>
                        <input id="title" name="title" type="text" value="<?php echo html_escape($seo_data[$edit_slug]['title'] ?? ''); ?>" placeholder="Enter Page Title" required>
                        <small style="color: #64748b; margin-top: 4px; display: block;">Optimal length for Google is 50-60 characters.</small>
                    </div>
                    <div class="form-group" style="margin-top: 1.5rem;">
                        <label for="description">Meta Description Tag</label>
                        <textarea id="description" name="description" rows="4" placeholder="Enter Description" required style="width: 100%; padding: 0.875rem 1rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; font-size: 0.95rem; resize: vertical;"><?php echo html_escape($seo_data[$edit_slug]['description'] ?? ''); ?></textarea>
                        <small style="color: #64748b; margin-top: 4px; display: block;">Optimal length for Google is 150-160 characters.</small>
                    </div>
                    <div style="margin-top: 2rem;">
                        <button type="submit" class="btn btn-primary">Save SEO Settings</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="admin-card">
                <p style="margin-bottom: 1.5rem; color: #4b5563;">Select a page below to configure its metadata parameters.</p>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Page Name</th>
                                <th>SEO Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pages as $slug => $name): ?>
                                <tr>
                                    <td><strong><?php echo html_escape($name); ?></strong><br><small style="color: #64748b;">/<?php echo html_escape($slug); ?><?php echo $slug === 'index' ? '' : '.php'; ?></small></td>
                                    <td>
                                        <?php if (isset($seo_data[$slug]) && (!empty($seo_data[$slug]['title']) || !empty($seo_data[$slug]['description']))): ?>
                                            <span class="status-pill status-published">Configured</span>
                                        <?php else: ?>
                                            <span class="status-pill status-draft">Default</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="action-links">
                                        <a href="seo-manager.php?edit=<?php echo html_escape($slug); ?>">Edit Settings</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>