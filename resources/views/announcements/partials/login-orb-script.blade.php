<script>
    (() => {
        const orb = document.querySelector('[data-bd-login-orb]');
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (!orb || reduceMotion) return;

        const animateOrb = (timestamp) => {
            const width = window.innerWidth;
            const height = window.innerHeight;
            const time = timestamp / 1000;
            const centerX = width * 0.5;
            const centerY = height * 0.52;
            const radiusX = Math.max(width * 0.27, 220);
            const radiusY = Math.max(height * 0.24, 150);
            const drift = Math.sin(time * 0.22) * 60;
            const x = centerX + Math.cos(time * 0.34) * radiusX + drift;
            const y = centerY + Math.sin(time * 0.42) * radiusY;
            const pulse = 0.86 + Math.sin(time * 1.35) * 0.1;

            orb.style.transform = `translate3d(${x}px, ${y}px, 0) translate(-50%, -50%) scale(${pulse})`;
            requestAnimationFrame(animateOrb);
        };

        requestAnimationFrame(animateOrb);
    })();
</script>
