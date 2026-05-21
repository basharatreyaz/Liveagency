<?php 
// Include your modular header
require_once 'includes/header.php'; 
?>
<link rel="stylesheet" href="assets/css/pricing.css">
<!-- Pricing Hero Section -->
<section class="pricing-hero">
    <div class="container text-center">
        <span class="section-tag">Plans & Pricing</span>
        <h1 class="page-title">Simple Plans. Powerful Support.</h1>
        <p class="page-subtitle">Choose a plan that fits your business needs. All plans include automated daily backups, proactive security patching, and core updates as a standard pipeline metric.</p>
    </div>
</section>

<!-- Pricing Matrix Grid Section -->
<section class="pricing-matrix-section">
    <div class="container">
        <div class="pricing-page-grid">
            
            <!-- Plan 1: Personal -->
            <div class="pricing-tier-card">
                <div class="card-top-meta">
                    <div class="plan-name">Personal</div>
                    <div class="plan-desc">The foundation of our care services. Perfect for static WordPress sites requiring proactive maintenance but minimal layout updates.</div>
                    <div class="plan-price-block">
                        <span class="currency">$</span>
                        <span class="price">15.99</span>
                        <span class="period">/mo per site</span>
                    </div>
                </div>
                <div class="card-features-block">
                    <ul class="tier-features-list">
                        <li><i class="fa-solid fa-check"></i> Automated Cloud Backups</li>
                        <li><i class="fa-solid fa-check"></i> Core, Theme & Plugin Updates</li>
                        <li><i class="fa-solid fa-check"></i> Firewall Request Filtering</li>
                        <li><i class="fa-solid fa-check"></i> Brute Force Login Protection</li>
                        <li><i class="fa-solid fa-check"></i> Malware Scanning & Cleanup</li>
                    </ul>
                </div>
                <div class="card-action-block">
                    <a href="contact.php?plan=personal" class="btn btn-outline">Get Started</a>
                </div>
            </div>

            <!-- Plan 2: Freelancer (Popular Tier Highlighted) -->
            <div class="pricing-tier-card popular-tier">
                <div class="popular-badge">Most Popular</div>
                <div class="card-top-meta">
                    <div class="plan-name text-primary">Freelancer</div>
                    <div class="plan-desc">Our most sought-after tier. Built for dynamic environments requiring fast delivery intervals, script edits, and zero live downtime.</div>
                    <div class="plan-price-block">
                        <span class="currency">$</span>
                        <span class="price">25.99</span>
                        <span class="period">/mo per site</span>
                    </div>
                </div>
                <div class="card-features-block">
                    <ul class="tier-features-list">
                        <li><i class="fa-solid fa-check"></i> <strong>Everything in Personal, Plus:</strong></li>
                        <li><i class="fa-solid fa-check"></i> Premium Brizy & Builder Support</li>
                        <li><i class="fa-solid fa-check"></i> Edit Content on Existing Pages</li>
                        <li><i class="fa-solid fa-check"></i> Troubleshooting Dashboard Bugs</li>
                        <li><i class="fa-solid fa-check"></i> Core WooCommerce Asset Support</li>
                        <li><i class="fa-solid fa-check"></i> 30-Min Help Desk Task Window</li>
                    </ul>
                </div>
                <div class="card-action-block">
                    <a href="contact.php?plan=freelancer" class="btn btn-primary">Get Started</a>
                </div>
            </div>

            <!-- Plan 3: Agency -->
            <div class="pricing-tier-card">
                <div class="card-top-meta">
                    <div class="plan-name">Agency</div>
                    <div class="plan-desc">The ultimate management tier. Engineered for agency white-label pipelines, priority ticket queues, and extensive layouts.</div>
                    <div class="plan-price-block">
                        <span class="currency">$</span>
                        <span class="price">39.99</span>
                        <span class="period">/mo per site</span>
                    </div>
                </div>
                <div class="card-features-block">
                    <ul class="tier-features-list">
                        <li><i class="fa-solid fa-check"></i> <strong>Everything in Freelancer, Plus:</strong></li>
                        <li><i class="fa-solid fa-check"></i> Publish New Landing Pages</li>
                        <li><i class="fa-solid fa-check"></i> Update Structural Page Layouts</li>
                        <li><i class="fa-solid fa-check"></i> WordPress LMS Support (LearnDash)</li>
                        <li><i class="fa-solid fa-check"></i> Multisite Network Infrastructure</li>
                        <li><i class="fa-solid fa-check"></i> Dedicated Account Engineer</li>
                    </ul>
                </div>
                <div class="card-action-block">
                    <a href="contact.php?plan=agency" class="btn btn-outline">Get Started</a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- enterprise scale outreach banner -->
<section class="pricing-enterprise-cta">
    <div class="container text-center">
        <h2>Managing 5 or more WordPress websites?</h2>
        <p>We provide high-volume discount frameworks and dedicated cloud server nodes specifically scaled for multi-tenant digital portfolios.</p>
        <a href="contact.php?tier=enterprise" class="btn btn-primary">Request Custom Agency Pricing</a>
    </div>
</section>

<?php 
// Include your modular footer
require_once 'includes/footer.php'; 
?>