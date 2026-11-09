;(function(){
function initFireflies() {
    var c = window.DFEST;
    if (!c || !c.enabled || !c.ff) {
        return;
    }
    var C = c.ff;

    const container = document.getElementById('magicContainer') || (() => {
        const c = document.createElement('div');
        c.id = 'magicContainer';
        document.body.appendChild(c);
        return c;
    })();

    const toggle = document.getElementById('magicToggle') || (() => {
        const b = document.createElement('button');
        b.id = 'magicToggle';
        document.body.appendChild(b);
        return b;
    })();

    const fireflies = [];
    const fireflyColors = [
        'rgba(120,200,255,1)',
        'rgba(150,230,255,1)',
        'rgba(255,255,255,1)'
    ];

    const settings = {
        maxFireflies: C.count || 20,
        speed: (C.speed || 3) / 3,
        mode: C.mode || 'always' // always | first
    };

    const stored = localStorage.getItem('fireflies_enabled');
    let magicEnabled;

    if (stored === null) {
        magicEnabled = true;
        localStorage.setItem('fireflies_enabled', 'true');
    } else {
        magicEnabled = stored === 'true';
    }

    toggle.textContent = magicEnabled ? '✨ ON' : '☆ OFF';

    const firstSessionGlow = !sessionStorage.getItem('glow_shown');

    function createFirefly() {
        if (!magicEnabled || document.hidden || fireflies.length >= settings.maxFireflies) return;

        const f = document.createElement('div');
        f.className = 'firefly';

        const size = 3 + Math.random() * 2;
        f.style.width = f.style.height = size + 'px';
        f.style.left = Math.random() * window.innerWidth + 'px';
        f.style.top = Math.random() * window.innerHeight + 'px';
        f.style.background = f.style.color =
            fireflyColors[Math.floor(Math.random() * fireflyColors.length)];

        f.style.animationDuration =
            (4 + Math.random() * 4) / settings.speed + 's';

        container.appendChild(f);
        fireflies.push(f);

        setTimeout(() => {
            f.remove();
            const i = fireflies.indexOf(f);
            if (i > -1) fireflies.splice(i, 1);
        }, parseFloat(f.style.animationDuration) * 1000);
    }

    function fadeOutFireflies() {
        fireflies.forEach((f, i) => {
            setTimeout(() => {
                f.style.opacity = 0;
                setTimeout(() => {
                    const k = fireflies.indexOf(f);
                    if (k > -1) {
                        f.remove();
                        fireflies.splice(k, 1);
                    }
                }, 1000);
            }, i * 120);
        });
    }

    function attentionToToggleLong(duration = 30000, interval = 500) {
        let elapsed = 0;
        const blink = setInterval(() => {
            toggle.classList.remove('attention');
            void toggle.offsetWidth;
            toggle.classList.add('attention');
            elapsed += interval;
            if (elapsed >= duration) {
                toggle.classList.remove('attention');
                clearInterval(blink);
            }
        }, interval);
    }

    function spawnHintFirefly() {
        const r = toggle.getBoundingClientRect();
        const f = document.createElement('div');
        f.className = 'firefly hint';
        f.style.width = f.style.height = '4px';
        f.style.left = r.left + r.width / 2 + 'px';
        f.style.top = r.top - 8 + 'px';
        f.style.background = f.style.color = 'rgba(255,255,255,1)';
        container.appendChild(f);
        setTimeout(() => f.remove(), 2200);
    }

    function runIdleGlowOnce() {
        toggle.classList.add('idle');
        setTimeout(() => toggle.classList.add('fadeout'), 500);
        setTimeout(() => toggle.classList.remove('idle', 'fadeout'), 1000);
    }

    if (
        settings.mode === 'first' &&
        magicEnabled &&
        !sessionStorage.getItem('firstSessionTimeout')
    ) {
        sessionStorage.setItem('firstSessionTimeout', '1');

        setTimeout(() => {
            magicEnabled = false;
            localStorage.setItem('fireflies_enabled', 'false');
            toggle.textContent = '☆ OFF';

            fadeOutFireflies();
            attentionToToggleLong(30000, 500);
            spawnHintFirefly();

            if (firstSessionGlow) {
                sessionStorage.setItem('glow_shown', '1');
                runIdleGlowOnce();
            }
        }, 60000);
    }

    toggle.addEventListener('click', () => {
        magicEnabled = !magicEnabled;
        localStorage.setItem('fireflies_enabled', magicEnabled ? 'true' : 'false');
        toggle.textContent = magicEnabled ? '✨ ON' : '☆ OFF';
        if (!magicEnabled) fadeOutFireflies();
    });

    setInterval(createFirefly, 100);
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFireflies);
} else {
    initFireflies();
}
})();
