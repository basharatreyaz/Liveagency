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
});