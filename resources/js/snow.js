;(function(){
  var c = window.DFEST;
  if (!c || !c.enabled || !c.snow) {
    return;
  }
  var cfg = c.snow;
  const SNOW_CONFIG = {
    initialFlakes: cfg.initial || 140,
    flakesOnReenable: cfg.initial || 140,
    maxFlakes: cfg.max || 180,
    lockToFooter: true,
    footerMargin: 15
  };

  const disableKey = "snowDisabledUntil";
  const now = Date.now();
  const disabledUntil = localStorage.getItem(disableKey);

  let snowActive = true;
  let flakes = [];
  let currentThemeLight = false;

  if (disabledUntil && Number(disabledUntil) > now) {
    snowActive = false;
  }

  function isLightTheme() {
    if (document.body.classList.contains('App--light')) return true;
    if (document.documentElement.classList.contains('light')) return true;

    const rootStyle = getComputedStyle(document.documentElement);
    const bgVar = rootStyle.getPropertyValue('--body-bg') || rootStyle.getPropertyValue('--bg-color');
    if (bgVar && bgVar.includes('#')) {
      const hex = bgVar.trim().replace('#', '');
      if (hex.length >= 6) {
        const r = parseInt(hex.substr(0,2), 16);
        const g = parseInt(hex.substr(2,2), 16);
        const b = parseInt(hex.substr(4,2), 16);
        const brightness = (r * 299 + g * 587 + b * 114) / 1000;
        return brightness > 140;
      }
    }

    const bg = getComputedStyle(document.body).backgroundColor;
    const match = bg.match(/\d+/g);
    if (match && match.length >= 3) {
      const brightness = (parseInt(match[0]) * 299 + parseInt(match[1]) * 587 + parseInt(match[2]) * 114) / 1000;
      return brightness > 140;
    }

    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) return true;
    return false;
  }

  function updateThemeAppearance() {
    const btn = document.getElementById('toggleSnow');
    if (!btn) return;

    const newIsLight = isLightTheme();
    currentThemeLight = newIsLight;

    if (newIsLight) {
      btn.classList.add('light-theme');
    } else {
      btn.classList.remove('light-theme');
    }

    flakes.forEach(f => {
      f.opacity = newIsLight ? Math.max(f.opacity, 0.55) : Math.min(f.opacity, 0.7);
    });
  }

  function initSnow() {
    currentThemeLight = isLightTheme();
    createToggleButton(snowActive, currentThemeLight);
    startSnow(currentThemeLight);

    if (window.matchMedia) {
      const themeQuery = window.matchMedia('(prefers-color-scheme: light)');
      themeQuery.addEventListener('change', updateThemeAppearance);
    }

    if (document.body) {
      const bodyObserver = new MutationObserver((mutations) => {
        for (const mutation of mutations) {
          if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
            updateThemeAppearance();
            break;
          }
        }
      });
      bodyObserver.observe(document.body, { attributes: true });
    }

    setInterval(updateThemeAppearance, 2000);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSnow);
  } else {
    initSnow();
  }

  function startSnow(isLight) {
    const canvas = document.getElementById('snow') || createCanvas();
    const ctx = canvas.getContext("2d");

    let width = canvas.width = window.innerWidth;
    let height = canvas.height = window.innerHeight;

    if (snowActive) {
      addNewFlakes(SNOW_CONFIG.initialFlakes);
    }

    window.addEventListener('resize', () => {
      width = canvas.width = window.innerWidth;
      height = canvas.height = window.innerHeight;
    });

    function animate() {
      ctx.clearRect(0, 0, width, height);

      for (let flake of flakes) {
        if (flake.stopped) continue;

        const offsetX = Math.sin(flake.angle) * flake.swing;
        const speedFactor = flake.fastFall ? 5 : 1;

        flake.x += offsetX * 0.05;
        flake.y += flake.speed * flake.yoyo * speedFactor;
        flake.angle += 0.01;

        if (flake.y > height) {
          if (snowActive) {
            flake.y = -flake.radius;
            flake.x = Math.random() * width;
            flake.fastFall = false;
          } else {
            flake.y = height;
            flake.stopped = true;
          }
        }

        ctx.save();
        if (currentThemeLight) {
          ctx.shadowColor = 'rgba(20, 60, 100, 0.55)';
          ctx.shadowBlur = 4;
          ctx.shadowOffsetX = 0;
          ctx.shadowOffsetY = 1;
        } else {
          ctx.shadowColor = 'rgba(0, 0, 0, 0.4)';
          ctx.shadowBlur = 2;
          ctx.shadowOffsetX = 0;
          ctx.shadowOffsetY = 1;
        }

        ctx.beginPath();
        ctx.arc(flake.x, flake.y, flake.radius, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(255,255,255,${flake.opacity})`;
        ctx.fill();
        ctx.restore();
      }

      requestAnimationFrame(animate);
    }

    animate();
  }

  function createCanvas() {
    const canvas = document.createElement('canvas');
    canvas.id = 'snow';
    document.body.appendChild(canvas);
    return canvas;
  }

  function addNewFlakes(count) {
    const canvas = document.getElementById('snow');
    if (!canvas) return;
    const width = canvas.width;
    const height = canvas.height;

    const availableSlots = Math.max(0, SNOW_CONFIG.maxFlakes - flakes.length);
    const actualCount = Math.min(count, availableSlots);

    function random(min, max) { return min + Math.random() * (max - min); }
    function generateScale() {
      const r = Math.random();
      if (r < 0.6) return random(0.05, 0.3);
      if (r < 0.9) return random(0.3, 0.6);
      return random(0.6, 1);
    }

    for (let i = 0; i < actualCount; i++) {
      const scale = generateScale();
      const radius = Math.min(5, 5 * scale);

      flakes.push({
        x: random(0, width),
        y: random(-height, 0),
        radius: radius,
        opacity: currentThemeLight ? random(0.55, 0.9) : random(0.3, 0.7),
        speed: random(0.5, 1.0) * scale,
        angle: random(0, Math.PI * 2),
        swing: random(-10, 10),
        yoyo: random(0.3, 1),
        stopped: false,
        fastFall: false
      });
    }
  }

  function createToggleButton(isOn, isLight) {
    const btn = document.createElement("button");
    btn.id = "toggleSnow";

    if (isLight) btn.classList.add('light-theme');

    btn.innerHTML = isOn
      ? '<span class="snow-icon active">❄️</span> ON'
      : '<span class="snow-icon inactive">❄️</span> OFF';

    document.body.appendChild(btn);

    let isDragging = false;
    let hasMoved = false;
    let startX, startY;
    let currentX = 0, currentY = 0;
    let initialX = 0, initialY = 0;

    const savedPos = localStorage.getItem('snowBtnPos');
    if (savedPos) {
      try {
        const pos = JSON.parse(savedPos);
        currentX = pos.x;
        currentY = SNOW_CONFIG.lockToFooter ? 0 : pos.y;
        btn.style.transform = `translate(${currentX}px, ${currentY}px)`;
      } catch(e) {}
    }

    function onStart(e) {
      if (e.type === 'mousedown' && e.button !== 0) return;

      isDragging = true;
      hasMoved = false;
      const clientX = e.touches ? e.touches[0].clientX : e.clientX;
      const clientY = e.touches ? e.touches[0].clientY : e.clientY;

      startX = clientX;
      startY = clientY;
      initialX = currentX;
      initialY = currentY;

      btn.style.transition = 'none';
    }

    function onMove(e) {
      if (!isDragging) return;

      const clientX = e.touches ? e.touches[0].clientX : e.clientX;
      const clientY = e.touches ? e.touches[0].clientY : e.clientY;

      const dx = clientX - startX;
      const dy = SNOW_CONFIG.lockToFooter ? 0 : (clientY - startY);

      let newX = initialX + dx;
      let newY = initialY + dy;

      btn.style.transform = `translate(${newX}px, ${newY}px)`;
      const rect = btn.getBoundingClientRect();

      const margin = 15;

      if (rect.left < margin) {
        newX += (margin - rect.left);
      } else if (rect.right > window.innerWidth - margin) {
        newX -= (rect.right - (window.innerWidth - margin));
      }

      if (SNOW_CONFIG.lockToFooter) {
        newY = 0;
      } else {
        if (rect.top < margin) {
          newY += (margin - rect.top);
        } else if (rect.bottom > window.innerHeight - margin) {
          newY -= (rect.bottom - (window.innerHeight - margin));
        }
      }

      currentX = newX;
      currentY = newY;
      btn.style.transform = `translate(${currentX}px, ${currentY}px)`;
    }

    function onEnd() {
      if (!isDragging) return;
      isDragging = false;
      btn.style.transition = 'box-shadow 0.25s ease, background 0.25s ease, border-color 0.25s ease';

      localStorage.setItem('snowBtnPos', JSON.stringify({
        x: currentX,
        y: currentY
      }));
    }

    btn.addEventListener('mousedown', onStart);
    window.addEventListener('mousemove', onMove);
    window.addEventListener('mouseup', onEnd);

    btn.addEventListener('touchstart', onStart, { passive: false });
    window.addEventListener('touchmove', onMove, { passive: false });
    window.addEventListener('touchend', onEnd);

    btn.addEventListener("click", (e) => {
      if (hasMoved) {
        e.preventDefault();
        e.stopPropagation();
        return;
      }

      if (snowActive) {
        snowActive = false;
        localStorage.setItem(disableKey, Date.now() + 24 * 60 * 60 * 1000);
        flakes.forEach(f => { if (f.y >= 0) f.fastFall = true; });
        flakes = flakes.filter(f => f.y >= 0);
        btn.innerHTML = '<span class="snow-icon inactive">❄️</span> OFF';
      } else {
        snowActive = true;
        localStorage.removeItem(disableKey);
        flakes.forEach(f => f.fastFall = false);
        addNewFlakes(SNOW_CONFIG.flakesOnReenable);
        btn.innerHTML = '<span class="snow-icon active">❄️</span> ON';
      }
    });
  }
})();
