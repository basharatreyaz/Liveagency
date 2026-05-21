<?php
require_once 'config.php';
require_once 'includes/header.php';

$members = [];
try {
	$pdo = get_pdo();
	$members = $pdo->query('SELECT id, name, title, experience, image FROM team_members ORDER BY created_at DESC')->fetchAll();
} catch (Exception $e) {
	// silently continue with empty members
}
?>

<link rel="stylesheet" href="assets/css/team.css">

<section class="team-hero">
    <div class="container text-center">
        <span class="section-tag">Meet The Team</span>
        <h1 class="page-title">Our Engineers & Support Staff</h1>
        <p class="page-subtitle">A small team of seasoned engineers focused on WordPress reliability, security and performance.</p>
    </div>
</section>

<section class="team-grid-section">
    <div class="container">
        <?php if (empty($members)): ?>
            <div class="team-empty-state">No team members added yet.</div>
        <?php else: ?>
            <div class="team-grid">
                <?php foreach ($members as $m): ?>
                    <div class="team-card">
                        <?php if (!empty($m['image'])): ?>
                            <div class="team-image-wrapper">
                                <img src="<?php echo html_escape($m['image']); ?>" alt="<?php echo html_escape($m['name']); ?>">
                            </div>
                        <?php else: ?>
                            <div class="team-initials"><?php echo strtoupper(substr(html_escape($m['name']),0,1)); ?></div>
                        <?php endif; ?>
                        <div class="team-name"><?php echo html_escape($m['name']); ?></div>
                        <div class="team-title"><?php echo html_escape($m['title']); ?></div>
                        <?php if (!empty($m['experience'])): ?>
                            <div class="team-experience"><?php echo html_escape($m['experience']); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>