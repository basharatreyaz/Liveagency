<?php
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['admin_logged_in'])) {
    cms_redirect('login.php');
}
require_admin();

$page_title = 'Database Backups';
$page_alert = '';
$backup_dir = __DIR__ . '/data/backups';

// Ensure backup directory exists and is secured
try {
    if (!is_dir($backup_dir)) {
        mkdir($backup_dir, 0755, true);
        file_put_contents($backup_dir . '/.htaccess', "Order allow,deny\nDeny from all");
        file_put_contents($backup_dir . '/index.php', "<?php http_response_code(403); exit; ?>");
    }
} catch (Exception $e) {
    $page_alert = 'Failed to initialize backup directory.';
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        try {
            $backup_file = $backup_dir . '/backup_' . date('Y-m-d_H-i-s') . '.db';
            if (file_exists(DB_FILE)) {
                copy(DB_FILE, $backup_file);
                $_SESSION['admin_message'] = 'New backup successfully created.';
            } else {
                $_SESSION['admin_message'] = 'Source database not found. Backup failed.';
            }
        } catch (Exception $e) {
            $_SESSION['admin_message'] = 'Backup failed: ' . $e->getMessage();
        }
        cms_redirect('backups.php');
    }
    
    if ($action === 'delete') {
        $filename = basename($_POST['filename'] ?? '');
        $filepath = $backup_dir . '/' . $filename;
        if ($filename && file_exists($filepath) && strpos($filename, 'backup_') === 0 && pathinfo($filename, PATHINFO_EXTENSION) === 'db') {
            unlink($filepath);
            $_SESSION['admin_message'] = 'Backup deleted.';
        }
        cms_redirect('backups.php');
    }
    
    if ($action === 'download') {
        $filename = basename($_POST['filename'] ?? '');
        $filepath = $backup_dir . '/' . $filename;
        if ($filename && file_exists($filepath) && strpos($filename, 'backup_') === 0 && pathinfo($filename, PATHINFO_EXTENSION) === 'db') {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit;
        }
    }
    
    if ($action === 'restore') {
        $filename = basename($_POST['filename'] ?? '');
        $filepath = $backup_dir . '/' . $filename;
        if ($filename && file_exists($filepath) && strpos($filename, 'backup_') === 0 && pathinfo($filename, PATHINFO_EXTENSION) === 'db') {
            try {
                // Replace the active database file with the chosen backup snapshot
                if (copy($filepath, DB_FILE)) {
                    $_SESSION['admin_message'] = 'Database restored successfully from ' . html_escape($filename) . '.';
                } else {
                    $_SESSION['admin_message'] = 'Failed to restore database from ' . html_escape($filename) . '.';
                }
            } catch (Exception $e) {
                $_SESSION['admin_message'] = 'Restore failed: ' . $e->getMessage();
            }
        }
        cms_redirect('backups.php');
    }
}

if (!empty($_SESSION['admin_message'])) {
    $page_alert = html_escape($_SESSION['admin_message']);
    unset($_SESSION['admin_message']);
}

// Fetch existing backups
$backups = [];
if (is_dir($backup_dir)) {
    $files = glob($backup_dir . '/backup_*.db');
    foreach ($files as $file) {
        $backups[] = [
            'filename' => basename($file),
            'size' => filesize($file),
            'date' => filemtime($file)
        ];
    }
    // Sort descending by date (newest first)
    usort($backups, function($a, $b) {
        return $b['date'] - $a['date'];
    });
}

function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow]; 
}

include __DIR__ . '/includes/admin-header.php';
?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
        <div>
            <h2 style="margin:0; font-size:1.2rem; font-weight:700;">Database Backups</h2>
            <p style="margin:.5rem 0 0; color:#475569;">Manually create, download, and manage your database snapshots.</p>
        </div>
        <div>
            <form method="post" style="margin:0;">
                <input type="hidden" name="csrf_token" value="<?php echo html_escape(get_csrf_token()); ?>">
                <input type="hidden" name="action" value="create">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Create Backup Now</button>
            </form>
        </div>
    </div>

    <?php if ($page_alert): ?>
        <div class="alert" style="margin-top:1rem;">
            <?php echo $page_alert; ?>
        </div>
    <?php endif; ?>

    <div class="table-wrapper" style="margin-top:1.5rem;">
        <table>
            <thead>
                <tr>
                    <th>Filename</th>
                    <th>Date Created</th>
                    <th>File Size</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($backups)): ?>
                    <tr><td colspan="4" style="text-align:center; padding:2rem; color:#64748b;">No backups found yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($backups as $backup): ?>
                        <tr>
                            <td style="font-family:monospace;"><?php echo html_escape($backup['filename']); ?></td>
                            <td><?php echo date('M d, Y g:i A', $backup['date']); ?></td>
                            <td><?php echo formatBytes($backup['size']); ?></td>
                            <td class="action-links">
                                <form method="post" style="display:inline-block; margin:0;">
                                    <input type="hidden" name="csrf_token" value="<?php echo html_escape(get_csrf_token()); ?>">
                                    <input type="hidden" name="action" value="download">
                                    <input type="hidden" name="filename" value="<?php echo html_escape($backup['filename']); ?>">
                                    <button type="submit" class="btn btn-sm btn-secondary"><i class="fa-solid fa-download"></i> Download</button>
                                </form>
                                <form method="post" style="display:inline-block; margin:0;" onsubmit="return confirm('Are you sure you want to RESTORE this backup? This will instantly overwrite the current database and all recent changes will be lost.');">
                                    <input type="hidden" name="csrf_token" value="<?php echo html_escape(get_csrf_token()); ?>">
                                    <input type="hidden" name="action" value="restore">
                                    <input type="hidden" name="filename" value="<?php echo html_escape($backup['filename']); ?>">
                                    <button type="submit" class="btn btn-sm btn-success"><i class="fa-solid fa-clock-rotate-left"></i> Restore</button>
                                </form>
                                <form method="post" style="display:inline-block; margin:0;" onsubmit="return confirm('Are you sure you want to permanently delete this backup?');">
                                    <input type="hidden" name="csrf_token" value="<?php echo html_escape(get_csrf_token()); ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="filename" value="<?php echo html_escape($backup['filename']); ?>">
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i> Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
