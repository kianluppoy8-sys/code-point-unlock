/**
 * Code Point Unlock - Premium Interaction Core
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Enhanced Flash Messages
    const flashMessages = document.querySelectorAll('.alert');
    flashMessages.forEach((msg) => {
        // Add close button
        const closeBtn = document.createElement('span');
        closeBtn.innerHTML = '&times;';
        closeBtn.style.marginLeft = 'auto';
        closeBtn.style.cursor = 'pointer';
        closeBtn.style.fontSize = '1.2rem';
        closeBtn.onclick = () => removeMsg(msg);
        msg.appendChild(closeBtn);

        // Auto-remove
        setTimeout(() => removeMsg(msg), 6000);
    });

    function removeMsg(msg) {
        msg.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
        msg.style.opacity = '0';
        msg.style.transform = 'translateX(20px)';
        setTimeout(() => msg.remove(), 400);
    }

    // 2. Button Ripple Effect
    const buttons = document.querySelectorAll('.btn-primary, .btn-glow');
    buttons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            const x = e.clientX - e.target.offsetLeft;
            const y = e.clientY - e.target.offsetTop;

            const ripple = document.createElement('span');
            ripple.className = 'btn-ripple';
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;

            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
    });

    // 3. Scroll Progress Indicator (for Dashboard/Leaderboard)
    const dashboard = document.querySelector('.dashboard');
    if (dashboard) {
        const progress = document.createElement('div');
        progress.className = 'scroll-progress';
        document.body.appendChild(progress);

        window.addEventListener('scroll', () => {
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;
            progress.style.width = scrolled + "%";
        });
    }

    // 4. Console Auto-Scroll
    const terminal = document.getElementById('terminal-content');
    if (terminal) {
        terminal.scrollTop = terminal.scrollHeight;
    }
});

// Polyfill for CSS :has() alternatives or similar complex interactions can go here
