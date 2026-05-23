document.addEventListener('DOMContentLoaded', () => {
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