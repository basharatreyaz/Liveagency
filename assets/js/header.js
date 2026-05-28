document.addEventListener('DOMContentLoaded', () => {

    // Ensure header events are bound only once during SPA navigations
    if (window.__headerInitialized) return;
    window.__headerInitialized = true;

    // React-like SPA Navigation (Client-side routing without full page reload)
    document.addEventListener('click', async (e) => {
        const link = e.target.closest('a');
        if (!link || !link.href) return;
        
        try {
            const url = new URL(link.href);
            
            // Skip external links, new tabs, anchor links, or downloads
            if (url.origin !== window.location.origin || link.target === '_blank' || link.hasAttribute('download') || e.ctrlKey || e.metaKey) return;
            if (url.pathname === window.location.pathname && url.hash) return;
            
            // Skip CMS/Admin links to keep backend fully disconnected
            if (url.pathname.includes('/cms/')) return;

            e.preventDefault();

            // Display React/Next.js style top progress bar
            let progressBar = document.getElementById('react-loader-bar');
            if (!progressBar) {
                progressBar = document.createElement('div');
                progressBar.id = 'react-loader-bar';
                document.body.appendChild(progressBar);
            }
            
            progressBar.style.transition = 'none';
            progressBar.style.width = '10%';
            progressBar.style.opacity = '1';
            void progressBar.offsetWidth; // Force reflow
            
            progressBar.style.transition = 'width 0.4s ease';
            progressBar.style.width = '60%';

            const response = await fetch(url.href);
            if (!response.ok) throw new Error('Network error');
            const html = await response.text();
            
            progressBar.style.width = '90%';

            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Swap out main content only
            const currentMain = document.querySelector('main.site-content');
            const newMain = doc.querySelector('main.site-content');
            
            if (currentMain && newMain) {
                currentMain.innerHTML = newMain.innerHTML;
                currentMain.className = newMain.className;
            } else {
                window.location.href = url.href;
                return;
            }

            // Update Document Context and History
            document.title = doc.title;
            window.history.pushState({}, '', url.href);

            // Update Active Navigation State dynamically
            document.querySelectorAll('.main-nav a').forEach(navLink => {
                const linkUrl = new URL(navLink.href, window.location.origin);
                const linkPath = linkUrl.pathname.replace(/\/$/, '') || '/';
                const currentPath = url.pathname.replace(/\/$/, '') || '/';
                
                if (linkPath === currentPath) {
                    navLink.classList.add('active');
                } else {
                    navLink.classList.remove('active');
                }
            });

            // Inject any missing stylesheets dynamically (e.g. page-specific CSS)
            doc.querySelectorAll('link[rel="stylesheet"]').forEach(newLink => {
                const href = newLink.getAttribute('href');
                if (!document.querySelector(`link[href="${href}"]`)) {
                    const linkNode = document.createElement('link');
                    linkNode.rel = 'stylesheet';
                    linkNode.href = newLink.href;
                    document.head.appendChild(linkNode);
                }
            });
            
            // Inject missing scripts and wait for them to load before proceeding
            const scriptPromises = Array.from(doc.querySelectorAll('script')).map(newScript => {
                if (newScript.src && !document.querySelector(`script[src="${newScript.getAttribute('src')}"]`)) {
                    return new Promise(resolve => {
                        const scriptNode = document.createElement('script');
                        scriptNode.src = newScript.src;
                        scriptNode.onload = resolve;
                        scriptNode.onerror = resolve; // Resolve anyway to avoid blocking on 404s
                        document.body.appendChild(scriptNode);
                    });
                }
                return Promise.resolve();
            });

            await Promise.all(scriptPromises);

            progressBar.style.width = '100%';
            setTimeout(() => {
                progressBar.style.opacity = '0';
            }, 300);

            window.scrollTo(0, 0);

            // Re-trigger element bindings for page-specific features like your Homepage Counters
            document.dispatchEvent(new Event('DOMContentLoaded'));

        } catch (err) {
            window.location.href = link.href; // Fallback to normal navigation if fetch fails
        }
    });

    // Handle traditional back/forward button clicks cleanly
    window.addEventListener('popstate', () => window.location.reload());

    const hamburger = document.getElementById('mobile-menu-btn');
    const navMenu = document.getElementById('primary-nav');

    // Toggle menu open/close
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
    }

    // Close menu when clicking outside of it
    document.addEventListener('click', (e) => {
        if (!hamburger.contains(e.target) && !navMenu.contains(e.target) && navMenu.classList.contains('active')) {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
        }
    });

    // Search Modal Logic
    const searchModal = document.getElementById('search-modal');
    const searchToggleBtns = document.querySelectorAll('.search-toggle-btn');
    const closeSearchBtn = document.getElementById('close-search');
    const searchInput = document.getElementById('modal-search-input');
    const searchOverlay = document.querySelector('.search-modal-overlay');

    if (searchModal) {
        searchToggleBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                searchModal.classList.add('active');
                setTimeout(() => searchInput.focus(), 100);
            });
        });

        const closeSearch = () => searchModal.classList.remove('active');

        closeSearchBtn.addEventListener('click', closeSearch);
        searchOverlay.addEventListener('click', closeSearch);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && searchModal.classList.contains('active')) closeSearch();
        });
    }
});