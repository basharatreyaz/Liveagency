<?php 
require_once 'config.php';

$latest_posts = [];
try {
    $pdo = get_pdo();
    $stmt = $pdo->query('SELECT title, slug, excerpt, category, author, featured_image, created_at FROM posts WHERE status = "published" ORDER BY created_at DESC LIMIT 3');
    $latest_posts = $stmt->fetchAll();
} catch (Exception $e) {
    // Silently continue
}

// Include your modular header
require_once 'includes/header.php'; 
?>
<link rel="stylesheet" href="assets/css/index.css">

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-grid">
            <div class="hero-content">
                <span class="section-tag">WordPress Website Care Experts</span>
                <h1>WordPress Maintenance & Support Services</h1>
                <p>We keep your WordPress website fast, secure and up-to-date so you can focus on growing your business.</p>
                
                <div class="hero-features">
                    <div class="hero-feature-item"><i class="fa-regular fa-circle-check"></i> 24/7 WordPress Support</div>
                    <div class="hero-feature-item"><i class="fa-regular fa-circle-check"></i> Website Maintenance & Updates</div>
                    <div class="hero-feature-item"><i class="fa-regular fa-circle-check"></i> Security Monitoring & Malware Removal</div>
                    <div class="hero-feature-item"><i class="fa-regular fa-circle-check"></i> Backups & Performance Optimization</div>
                </div>

                <div class="hero-buttons">
                    <a href="pricing" class="btn btn-primary">See Our Plans</a>
                    <a href="audit" class="btn btn-outline">Get A Free Audit</a>
                </div>
            </div>

            <div class="hero-image-container">
                <svg class="hero-image-svg" viewBox="0 0 500 350" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="500" height="350" fill="#0A0F18"/>
                    <rect x="50" y="30" width="400" height="260" rx="12" fill="#1E293B" stroke="#334155" stroke-width="4"/>
                    <path d="M20 290H480L490 310H10L20 290Z" fill="#334155"/>
                    <rect x="210" y="295" width="80" height="6" rx="3" fill="#1E293B"/>
                    <rect x="70" y="50" width="360" height="220" rx="6" fill="#111827"/>
                    <rect x="85" y="65" width="100" height="12" rx="4" fill="#334155"/>
                    <rect x="85" y="90" width="210" height="8" rx="4" fill="#1E293B"/>
                    <rect x="310" y="65" width="120" height="60" rx="6" fill="#1E293B"/>
                    <circle cx="340" cy="95" r="15" fill="#10B981" fill-opacity="0.2"/>
                    <circle cx="340" cy="95" r="10" fill="#10B981"/>
                </svg>

                <div class="floating-badge badge-perf">
                    <div class="circle-progress">
                        <div class="circle-progress-inner" style="color: #10B981;">98</div>
                    </div>
                    <span class="badge-label">Excellent</span>
                </div>

                <div class="floating-badge badge-uptime">
                    <div class="circle-progress">
                        <div class="circle-progress-inner" style="color: #10B981;">100%</div>
                    </div>
                    <span class="badge-label">Uptime Monitor</span>
                </div>
            </div>
        </div>

        <div class="trusted-by">
            <div class="trusted-label">Trusted by 1,500+ Website Owners Worldwide</div>
            <div class="logo-flex">
                <span>Brizy</span>
                <span>WooCommerce</span>
                <span>Hostinger</span>
                <span>Astra</span>
                <span>Wp rocket</span>
            </div>
        </div>
    </div>
</section>

<!-- Stats Bar -->
<section class="stats-bar">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-icon"><i class="fa-solid fa-earth-americas"></i></div>
                <div>
                    <div class="stat-number" data-target="1500" data-suffix="+">0</div>
                    <div class="stat-label">Websites Maintained</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon"><i class="fa-solid fa-award"></i></div>
                <div>
                    <div class="stat-number" data-target="3" data-suffix="+">0</div>
                    <div class="stat-label">Years of WordPress Expertise</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon"><i class="fa-solid fa-bolt"></i></div>
                <div>
                    <div class="stat-number" data-target="330" data-suffix="%">0</div>
                    <div class="stat-label">Average Performance Improvement</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon"><i class="fa-regular fa-face-smile"></i></div>
                <div>
                    <div class="stat-number" data-target="350" data-suffix="+">0</div>
                    <div class="stat-label">Happy Clients</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section">
    <div class="container">
        <div class="services-top-layout">
            <div>
                <span class="section-tag">Monthly WordPress Maintenance</span>
                <h2 class="section-title">We Take Care of Your Website So You Don't Have To</h2>
                <p class="services-subtitle">Our WordPress experts handle everything from updates and security to backups and speed optimization.</p>
                <a href="services.php" class="btn btn-primary">View All Services</a>
            </div>

            <div class="services-grid-right">
                <div class="service-card">
                    <div class="service-icon-box"><i class="fa-solid fa-rotate"></i></div>
                    <h3>Core, Theme & Plugin Updates</h3>
                    <p>We keep your WordPress core, themes and plugins up-to-date for maximum security and compatibility.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon-box"><i class="fa-solid fa-shield-halved"></i></div>
                    <h3>WordPress Security & Malware Removal</h3>
                    <p>Advanced security solutions and malware removal to keep your website safe and secure.</p>
                </div>
            </div>
        </div>

        <div class="services-bottom-grid">
            <div class="service-card">
                <div class="service-icon-box"><i class="fa-solid fa-database"></i></div>
                <h3>Daily Backups & One-Click Restore</h3>
                <p>Automated daily backups with easy one-click restore to keep your data always protected.</p>
            </div>
            <div class="service-card">
                <div class="service-icon-box"><i class="fa-solid fa-gauge-high"></i></div>
                <h3>Performance Optimization</h3>
                <p>We optimize your website speed and performance for better user experience and SEO ranking.</p>
            </div>
            <div class="service-card">
                <div class="service-icon-box"><i class="fa-solid fa-magnifying-glass-chart"></i></div>
                <h3>SEO & Database Optimization</h3>
                <p>Clean, optimized and SEO friendly website that helps you rank higher and grow your business.</p>
            </div>
        </div>
    </div>
</section>

<!-- Process Section -->
<section class="process-section">
    <div class="container">
        <span class="section-tag">Our Proven Process</span>
        <h2 class="section-title">How It Works</h2>
        <p class="section-desc">A simple and transparent process to keep your WordPress website secure, fast and always online.</p>

        <div class="process-flow">
            <div class="process-arrow"></div>
            
            <div class="process-step">
                <div class="process-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                <h4>We Analyze</h4>
                <p>We perform a complex analysis of your website to identify issues and improvement areas.</p>
            </div>
            <div class="process-step">
                <div class="process-icon"><i class="fa-solid fa-lock"></i></div>
                <h4>We Secure</h4>
                <p>We implement security measures, updates and backups to keep your site protected.</p>
            </div>
            <div class="process-step">
                <div class="process-icon"><i class="fa-solid fa-sliders"></i></div>
                <h4>We Optimize</h4>
                <p>We optimize speed, database and overall performance for the best results.</p>
            </div>
            <div class="process-step">
                <div class="process-icon"><i class="fa-solid fa-headset"></i></div>
                <h4>We Support</h4>
                <p>Ongoing maintenance and 24/7 support to ensure your website runs perfectly.</p>
            </div>
        </div>

        <a href="about.php" class="btn btn-primary">See How We Work</a>
    </div>
</section>

<!-- Testimonial Section -->
<section class="testimonial-section">
    <div class="container">
        <div class="testimonial-grid">
            <div class="video-block">
                <svg class="video-placeholder-svg" viewBox="0 0 500 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="500" height="300" fill="#111827"/>
                    <rect x="40" y="40" width="420" height="220" rx="6" fill="#1F2937"/>
                    <circle cx="70" cy="70" r="15" fill="#FF6B00"/>
                    <rect x="100" y="62" width="120" height="8" rx="4" fill="#374151"/>
                    <rect x="100" y="74" width="80" height="6" rx="3" fill="#4B5563"/>
                </svg>
                <div class="play-btn"><i class="fa-solid fa-play" style="margin-left: 4px;"></i></div>
            </div>

            <div class="testimonial-content">
                <blockquote>"I would recommend WP Site Doctors to anyone who needs to maintain a WordPress website."</blockquote>
                <p>Their team is very professional, quick to respond and extremely knowledgeable. They fixed issues on our site within minutes and now we don't have to worry about updates, backups or security anymore.</p>
                
                <div class="author-box">
                    <div class="author-img">DJ</div>
                    <div>
                        <div class="author-name">David Johnson</div>
                        <div class="author-role">Business Owner</div>
                    </div>
                </div>

                <a href="#" class="btn btn-primary">See More Reviews</a>
            </div>
        </div>
    </div>
</section>


<!-- Secondary Stats Bar -->
<section class="alt-stats">
    <div class="container">
        <div class="alt-stats-grid">
            <div class="alt-stat-item">
                <div class="alt-stat-icon"><i class="fa-regular fa-face-smile"></i></div>
                <div class="alt-stat-num query-counter" data-target="350" data-suffix="+">0</div>
                <div class="alt-stat-lbl">Happy Clients</div>
            </div>
            <div class="alt-stat-item">
                <div class="alt-stat-icon"><i class="fa-solid fa-laptop-code"></i></div>
                <div class="alt-stat-num query-counter" data-target="1500" data-suffix="+">0</div>
                <div class="alt-stat-lbl">Websites Maintained</div>
            </div>
            <div class="alt-stat-item">
                <div class="alt-stat-icon"><i class="fa-solid fa-chart-line"></i></div>
                <div class="alt-stat-num query-counter" data-target="99.9" data-suffix="%" data-decimals="1">0</div>
                <div class="alt-stat-lbl">Average Uptime</div>
            </div>
            <div class="alt-stat-item">
                <div class="alt-stat-icon"><i class="fa-regular fa-clock"></i></div>
                <div class="alt-stat-num query-counter" data-target="3" data-suffix="+">0</div>
                <div class="alt-stat-lbl">Years Experience</div>
            </div>
        </div>
    </div>
</section>

<!-- Lead Generation Section -->
<section class="lead-section">
    <div class="container">
        <div class="lead-grid">
            <div>
                <span class="lead-tag">Free Download</span>
                <h2 class="lead-title">WordPress Website Maintenance Checklist</h2>
                <p class="lead-desc">A complete checklist to keep your WordPress website secure, fast and always up-to-date.</p>
                
                <div class="checklist-grid mb-30">
                    <div class="checklist-item"><i class="fa-regular fa-circle-check"></i> Security Checklist</div>
                    <div class="checklist-item"><i class="fa-regular fa-circle-check"></i> Performance Checklist</div>
                    <div class="checklist-item"><i class="fa-regular fa-circle-check"></i> Update Checklist</div>
                    <div class="checklist-item"><i class="fa-regular fa-circle-check"></i> Backup Checklist</div>
                    <div class="checklist-item"><i class="fa-regular fa-circle-check"></i> Daily Maintenance Tasks</div>
                </div>

                <a href="/#pop" class="btn btn-primary">Download Now <i class="fa-solid fa-arrow-down btn-icon-right"></i></a>
            </div>
            <div class="lead-mockup-container">
                <svg width="220" height="280" viewBox="0 0 220 280" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="10" y="10" width="200" height="260" rx="8" fill="#FFFFFF"/>
                    <rect x="10" y="10" width="200" height="40" rx="8" fill="#F3F4F6"/>
                    <circle cx="35" cy="30" r="6" fill="#FF6B00"/>
                    <rect x="55" y="26" width="100" height="8" rx="4" fill="#9CA3AF"/>
                    <rect x="30" y="80" width="16" height="16" rx="4" fill="#10B981" fill-opacity="0.2"/>
                    <rect x="60" y="84" width="110" height="8" rx="4" fill="#E5E7EB"/>
                    <rect x="30" y="120" width="16" height="16" rx="4" fill="#10B981" fill-opacity="0.2"/>
                    <rect x="60" y="124" width="90" height="8" rx="4" fill="#E5E7EB"/>
                    <circle cx="170" cy="230" r="30" fill="#0A0F18"/>
                    <text x="156" y="242" fill="white" font-family="sans-serif" font-size="36" font-weight="bold">W</text>
                </svg>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section">
    <div class="container">
        <div class="faq-grid">
            <div>
                <span class="section-tag">FAQs</span>
                <h2 class="section-title">Still Have Questions?</h2>
                <p>Our support team is here to help you 24/7.</p>
                <a href="contact.php" class="btn btn-primary">Contact Us</a>
            </div>

            <div>
                <div class="accordion-item">
                    <button class="accordion-header">
                        <span>What types of tasks are included in Help Desk Support?</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div class="accordion-content">
                        <p>If your website is on a Freelancer or Agency plan then you get Help Desk Support.</p>
                        <p>Help Desk Support tasks generally do include any WordPress issues or updates that can be completed within 30 minutes or less while logged in to your WordPress Dashboard, including:</p>
                        <ul>
                            <li>Fixing website errors and bugs</li>
                            <li>Edits to existing page content, copy and images</li>
                            <li>Install & configure plugins</li>
                            <li>User login & password management</li>
                        </ul>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-header">
                        <span>Can multiple websites receive support under one plan?</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div class="accordion-content">
                        <p>Each domain requires its own plan, either Personal, Freelancer, or Agency. If you have 3 websites that you would like us to maintain and manager, you’ll need 3 separate plans.</p>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <button class="accordion-header">
                        <span>Do you maintain and support ecommerce websites?</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div class="accordion-content">
                        <p>Yes, we absolutely LOVE e-commerce! Websites with advanced functionality like e-commerce require our Freelancer or Agency plan to properly manage them.</p>
                    </div>
                </div>
            </div>
        </div>
        <a href="faq.php" class="view-all-faqs">View All Articles &rarr;</a>
    </div>
</section>

<!-- Blog Section -->
<?php if (!empty($latest_posts)): ?>
<section class="blog-section">
    <div class="container">
        <div class="text-center mb-48">
            <span class="section-tag">Resources & Insights</span>
            <h2 class="section-title">Latest From Our Blog</h2>
        </div>
        <div class="blog-grid">
            <?php foreach ($latest_posts as $post): ?>
                <article class="blog-card flex-col">
                    <?php if (!empty($post['featured_image'])): ?>
                        <div class="blog-img-holder" style="background-image: url('<?php echo htmlspecialchars($post['featured_image']); ?>');"></div>
                    <?php else: ?>
                        <div class="blog-img-holder img-placeholder"></div>
                    <?php endif; ?>
                    <div class="blog-content flex-col flex-grow">
                        <span class="section-tag tag-left"><?php echo htmlspecialchars($post['category']); ?></span>
                        <h4 class="blog-title mb-16">
                            <a href="<?php echo htmlspecialchars($post['slug']); ?>" class="blog-link">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                        </h4>
                        <p class="blog-excerpt flex-grow">
                            <?php echo htmlspecialchars($post['excerpt']); ?>
                        </p>
                        <div class="blog-details meta-spaced">
                            <span><i class="fa-regular fa-calendar meta-icon"></i> <?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                            <span><i class="fa-regular fa-user meta-icon"></i> <?php echo htmlspecialchars($post['author']); ?></span>
                        </div>
                        <a href="<?php echo htmlspecialchars($post['slug']); ?>" class="blog-readmore">Read More &rarr;</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-64">
            <a href="blog.php" class="btn btn-outline">View All Articles</a>
        </div>
    </div>
</section>
<?php endif; ?>


<script src="assets/js/index.js"></script>
<?php 
// Include your modular footer
require_once 'includes/footer.php'; 
?>