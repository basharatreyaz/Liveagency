<?php 
// Include your modular header
require_once 'includes/header.php'; 
?>

<section style="background-color: var(--bg-light); padding: 6rem 2rem 3rem;">
    <div class="container text-center">
        <span class="section-tag">Legal Information</span>
        <h1 class="page-title">Refund Policy</h1>
        <p class="page-subtitle">Last updated: <?php echo date('F d, Y'); ?></p>
    </div>
</section>

<section style="background-color: var(--bg-light); border-top: 1px solid var(--border-color); padding: 4rem 2rem;">
    <div class="container" style="max-width: 800px; margin: 0 auto; color: var(--text-main); line-height: 1.8;">
        <h2 style="color: var(--heading-color); margin-top: 0; margin-bottom: 1rem;">1. 15-Day Money-Back Guarantee</h2>
        <p style="margin-bottom: 2rem;">At WP Site Doctors, we stand behind the quality of our WordPress maintenance and support services. If you are not completely satisfied with our service within the first 15 days of your initial purchase, you are eligible for a full refund.</p>

        <h2 style="color: var(--heading-color); margin-bottom: 1rem;">2. Eligibility Criteria</h2>
        <p style="margin-bottom: 1rem;">To qualify for a refund under our 15-day policy, the following conditions must be met:</p>
        <ul style="margin-bottom: 2rem; padding-left: 1.5rem;">
            <li style="margin-bottom: 0.5rem;">The refund request must be submitted within exactly 15 days of your original signup date.</li>
            <li style="margin-bottom: 0.5rem;">The request applies only to your first subscription billing cycle. Subsequent renewals are not eligible for the 15-day guarantee.</li>
            <li style="margin-bottom: 0.5rem;">Custom one-off development work, malware removal (if successfully completed), or specialized out-of-scope projects are non-refundable once work has commenced.</li>
        </ul>

        <h2 style="color: var(--heading-color); margin-bottom: 1rem;">3. How to Request a Refund</h2>
        <p style="margin-bottom: 2rem;">To initiate a refund, please contact our support team at <a href="mailto:support@wpsitedoctors.com" style="color: var(--primary); text-decoration: none; font-weight: 600;">support@wpsitedoctors.com</a> with your account details and a brief explanation of why the service did not meet your expectations. We process all eligible refund requests within 3-5 business days.</p>

        <h2 style="color: var(--heading-color); margin-bottom: 1rem;">4. Post-Refund Protocol</h2>
        <p style="margin-bottom: 2rem;">Upon processing your refund, your subscription will be immediately canceled. We will remove our proprietary maintenance plugins and revoke any administrative access we hold to your WordPress environment. You will retain full control of your website.</p>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>