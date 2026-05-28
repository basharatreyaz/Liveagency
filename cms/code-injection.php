<?php
require_once __DIR__ . '/../config.php';

if (!is_logged_in()) {
    cms_redirect('login.php');
}
require_admin();

$page_title = 'Code Injection';
$page_alert = '';
$success_msg = '';

try {
    $pdo = get_pdo();

    // Ensure the table and row exist for settings
    $pdo->exec('CREATE TABLE IF NOT EXISTS site_settings (id INTEGER PRIMARY KEY, header_code TEXT, body_code TEXT, footer_code TEXT)');
    $stmt = $pdo->query('SELECT COUNT(*) FROM site_settings WHERE id = 1');
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec('INSERT INTO site_settings (id, header_code, body_code, footer_code) VALUES (1, "", "", "")');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_csrf();
        
        $header_code = $_POST['header_code'] ?? '';
        $body_code = $_POST['body_code'] ?? '';
        $footer_code = $_POST['footer_code'] ?? '';

        $stmt = $pdo->prepare('UPDATE site_settings SET header_code = ?, body_code = ?, footer_code = ? WHERE id = 1');
        $stmt->execute([$header_code, $body_code, $footer_code]);
        
        $success_msg = 'Global code injection settings have been updated successfully.';
    }

    // Fetch current settings
    $stmt = $pdo->query('SELECT header_code, body_code, footer_code FROM site_settings WHERE id = 1');
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$settings) {
        $settings = ['header_code' => '', 'body_code' => '', 'footer_code' => ''];
    }

} catch (Exception $e) {
    $page_alert = 'Error: ' . html_escape($e->getMessage());
    $settings = ['header_code' => '', 'body_code' => '', 'footer_code' => ''];
}

include __DIR__ . '/includes/admin-header.php';
?>
<section class="admin-panel">
    <h1 style="margin:0 0 1.5rem 0;">Global Code Injection</h1>
    
    <?php if ($page_alert): ?><div class="alert alert-error"><?php echo $page_alert; ?></div><?php endif; ?>
    <?php if ($success_msg): ?>
        <div class="alert alert-success" style="background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
            <strong><i class="fa-solid fa-circle-check" style="margin-right: 6px;"></i>Success:</strong> <?php echo html_escape($success_msg); ?>
        </div>
    <?php endif; ?>

    <div class="admin-card">
        <p style="margin-bottom: 1.5rem; color: #4b5563;">Add custom scripts or code to your site's header, body, and footer. This is useful for analytics, tracking pixels, or custom styles.</p>
        <form method="post" action="code-injection.php" style="display:grid; gap:1.5rem;">
            <input type="hidden" name="csrf_token" value="<?php echo html_escape(get_csrf_token()); ?>">
            
            <div class="form-group"><label for="header_code">Header Code (before &lt;/head&gt;)</label><textarea id="header_code" name="header_code" rows="8" placeholder="e.g. <style>...</style> or <script>...</script>" style="width: 100%; padding: 0.875rem 1rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: monospace; font-size: 0.9rem; resize: vertical;"><?php echo html_escape($settings['header_code']); ?></textarea></div>
            <div class="form-group"><label for="body_code">Body Start Code (after &lt;body&gt;)</label><textarea id="body_code" name="body_code" rows="8" placeholder="e.g. Google Tag Manager (noscript) snippet" style="width: 100%; padding: 0.875rem 1rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: monospace; font-size: 0.9rem; resize: vertical;"><?php echo html_escape($settings['body_code']); ?></textarea></div>
            <div class="form-group"><label for="footer_code">Footer Code (before &lt;/body&gt;)</label><textarea id="footer_code" name="footer_code" rows="8" placeholder="e.g. Analytics scripts, chat widgets" style="width: 100%; padding: 0.875rem 1rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: monospace; font-size: 0.9rem; resize: vertical;"><?php echo html_escape($settings['footer_code']); ?></textarea></div>
            
            <div style="margin-top: 1rem;"><button type="submit" class="btn btn-primary">Save Code Settings</button></div>
        </form>
    </div>
</section>
<?php include __DIR__ . '/includes/admin-footer.php'; ?>