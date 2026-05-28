// ==========================================================================
// Homepage Interactive Features (Accordions & Statistics Counters)
// ==========================================================================

document.addEventListener('DOMContentLoaded', () => {
    
    /* ----------------------------------------------------------------------
       1. FAQ Accordion Toggle Logic
       ---------------------------------------------------------------------- */
    const accordionHeaders = document.querySelectorAll('.accordion-header');

    accordionHeaders.forEach(header => {
        header.addEventListener('click', () => {
            const currentItem = header.parentElement;
            const content = header.nextElementSibling;
            const icon = header.querySelector('i');
            
            // Optional: Close other open accordion items (Single-open mode)
            document.querySelectorAll('.accordion-item').forEach(item => {
                if (item !== currentItem && item.classList.contains('active')) {
                    item.classList.remove('active');
                    item.querySelector('.accordion-content').style.maxHeight = null;
                    const otherIcon = item.querySelector('.accordion-header i');
                    if (otherIcon) {
                        otherIcon.style.transform = 'rotate(0deg)';
                    }
                }
            });

            // Toggle active class on the clicked item
            currentItem.classList.toggle('active');

            if (currentItem.classList.contains('active')) {
                // Dynamically set max-height based on scroll height for smooth transition
                content.style.maxHeight = content.scrollHeight + "px";
                if (icon) icon.style.transform = 'rotate(180deg)';
            } else {
                // Collapse the section safely
                content.style.maxHeight = null;
                if (icon) icon.style.transform = 'rotate(0deg)';
            }
        });
    });

    /* ----------------------------------------------------------------------
       2. Animated Statistics Counters Logic
       ---------------------------------------------------------------------- */
    // Note: This matches both '.stat-number' and '.alt-stat-num' elements from your HTML
    const counterElements = document.querySelectorAll('.stat-number, .alt-stat-num');
    
    const animateCounter = (counter) => {
        // Parse setup attributes directly from your markup
        const targetAttr = counter.getAttribute('data-target');
        if (!targetAttr) return; // Safeguard if a static string is passed

        const target = parseFloat(targetAttr.replace(/,/g, ''));
        if (isNaN(target)) return;
        const suffix = counter.getAttribute('data-suffix') || '';
        const decimals = parseInt(counter.getAttribute('data-decimals')) || 0;
        
        // Configuration options for pacing the scroll animation
        const duration = 2000; // Animation runway lasts exactly 2 seconds
        const frameRate = 1000 / 60; // Target smooth 60fps rendering context
        const totalFrames = Math.round(duration / frameRate);
        
        let currentFrame = 0;

        const countUp = () => {
            currentFrame++;
            
            // Easing function: Ease-Out Quad to smoothly slow down near the target
            const progress = currentFrame / totalFrames;
            const easeProgress = progress * (2 - progress);
            
            const currentValue = easeProgress * target;

            // Format numerical updates safely over the run cycle
            counter.innerText = currentValue.toFixed(decimals) + suffix;

            if (currentFrame < totalFrames) {
                requestAnimationFrame(countUp);
            } else {
                // Ensure explicit target values sit clean on final render frames
                counter.innerText = target.toFixed(decimals) + suffix;
            }
        };

        requestAnimationFrame(countUp);
    };

    /* ----------------------------------------------------------------------
       3. Intersection Observer (Trigger counters only when visible on screen)
       ---------------------------------------------------------------------- */
    const observerOptions = {
        root: null, // Viewport standard configuration boundary
        threshold: 0.2 // Fires up execution pipelines once 20% of block is captured
    };

    const counterObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                observer.unobserve(entry.target); // Stops viewing element once animation finishes
            }
        });
    }, observerOptions);

    // Bind observation listeners cleanly to active targets
    counterElements.forEach(counter => counterObserver.observe(counter));
});