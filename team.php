<?php
require_once 'config.php';
require_once 'includes/header.php';

$members = [];
try {
	$pdo = get_pdo();
	$members = $pdo->query('SELECT id, name, title, experience, image, details, linkedin, instagram, facebook, display_order FROM team_members ORDER BY display_order ASC, created_at DESC')->fetchAll();
} catch (Exception $e) {
	// silently continue with empty members
}
?>

<link rel="stylesheet" href="assets/css/team.css">
<style>
    .team-card.flip-card {
        background-color: transparent !important;
        perspective: 1000px;
        padding: 0 !important;
        border: none !important;
        box-shadow: none !important;
        height: 320px;
    }
    .team-card-inner {
        position: relative;
        width: 100%;
        height: 100%;
        text-align: center;
        transition: transform 0.6s;
        transform-style: preserve-3d;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border-radius: 12px;
        border: 1px solid var(--border-color, #e2e8f0);
    }
    .flip-card:hover .team-card-inner {
        transform: rotateY(180deg);
    }
    .team-card-front, .team-card-back {
        position: absolute;
        width: 100%;
        height: 100%;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        border-radius: 12px;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 2rem;
        box-sizing: border-box;
    }
    .team-card-back {
        transform: rotateY(180deg);
        background-color: var(--bg-light, #f8fafc);
        text-align: center;
    }
    .team-card-back h3 { margin: 0 0 1rem 0; color: var(--heading-color); font-size: 1.25rem; }
    .team-card-back p { color: var(--text-main, #475569); font-size: 0.95rem; line-height: 1.6; margin: 0; overflow-y: auto; }
    html.dark-theme .team-card-front, html.dark-theme .team-card-back { background: #1e293b; border-color: #334155; }
    .team-social-links {
        margin-top: 1rem;
        display: flex;
        gap: 1rem;
        justify-content: center;
    }
    .team-social-links a {
        color: var(--text-main, #475569);
        font-size: 1.25rem;
        transition: color 0.3s ease;
    }
    .team-social-links a:hover {
        color: var(--primary, #0073aa);
    }
</style>

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
                    <div class="team-card flip-card">
                        <div class="team-card-inner">
                            <div class="team-card-front">
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
                            <div class="team-card-back">
                                <h3><?php echo html_escape($m['name']); ?></h3>
                                <p><?php echo nl2br(html_escape($m['details'] ?? 'No bio available yet.')); ?></p>
                                <?php if (!empty($m['linkedin']) || !empty($m['instagram']) || !empty($m['facebook'])): ?>
                                    <div class="team-social-links">
                                        <?php if (!empty($m['linkedin'])): ?>
                                            <a href="<?php echo html_escape($m['linkedin']); ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn Profile"><i class="fa-brands fa-linkedin"></i></a>
                                        <?php endif; ?>
                                        <?php if (!empty($m['instagram'])): ?>
                                            <a href="<?php echo html_escape($m['instagram']); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram Profile"><i class="fa-brands fa-instagram"></i></a>
                                        <?php endif; ?>
                                        <?php if (!empty($m['facebook'])): ?>
                                            <a href="<?php echo html_escape($m['facebook']); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook Profile"><i class="fa-brands fa-facebook"></i></a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>