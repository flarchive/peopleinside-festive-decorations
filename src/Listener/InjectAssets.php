<?php

namespace PeopleInside\FestiveDecorations\Listener;

use Flarum\Frontend\Document;
use PeopleInside\FestiveDecorations\DateHelper;

class InjectAssets
{
    public function __invoke(Document $document)
    {
        $enabled = (bool) settings()->get('peopleinside-festive-decorations.enabled', false);
        if (!$enabled) {
            return;
        }

        $holidaysJson = settings()->get('peopleinside-festive-decorations.holidays', '["natale","capodanno"]');
        $allowedHolidays = json_decode($holidaysJson, true);
        if (!is_array($allowedHolidays)) {
            $allowedHolidays = ['natale', 'capodanno'];
        }

        $activeTheme = DateHelper::theme_for_today($allowedHolidays);
        $today = DateHelper::today();

        $snow_start_md = DateHelper::parse_day_month(settings()->get('peopleinside-festive-decorations.snow_start', '25/10'), 25, 10);
        $snow_end_md = DateHelper::parse_day_month(settings()->get('peopleinside-festive-decorations.snow_end', '07/01'), 7, 1);
        $snow_always = (bool) settings()->get('peopleinside-festive-decorations.snow_always', false);
        $snow_active = $snow_always || DateHelper::is_date_in_range($today, $snow_start_md, $snow_end_md);

        $ff_start_md = DateHelper::parse_day_month(settings()->get('peopleinside-festive-decorations.ff_start', '08/01'), 8, 1);
        $ff_end_md = DateHelper::parse_day_month(settings()->get('peopleinside-festive-decorations.ff_end', '24/10'), 24, 10);
        $ff_always = (bool) settings()->get('peopleinside-festive-decorations.ff_always', false);
        $ff_active = $ff_always || DateHelper::is_date_in_range($today, $ff_start_md, $ff_end_md);

        $has_natale = ($activeTheme === 'natale');
        $has_snow = (bool) settings()->get('peopleinside-festive-decorations.snow_enabled', false) && $snow_active;
        $has_fireflies = (bool) settings()->get('peopleinside-festive-decorations.fireflies_enabled', false) && $ff_active;

        // Natale include già la neve nel suo pacchetto, quindi se è Natale, iniettiamo tutto il pacchetto Natale
        if ($has_natale) {
            $document->head[] = '<style>' . $this->getNataleCss() . '</style>';
            $document->foot[] = '<script>' . $this->getNataleAvatarJs() . $this->getSnowJs() . '</script>';
        } else {
            // Altrimenti, iniettiamo solo la neve se abilitata separatamente
            if ($has_snow) {
                $document->head[] = '<style>' . $this->getSnowCss() . '</style>';
                $document->foot[] = '<script>' . $this->getSnowJs() . '</script>';
            }
        }

        // Le lucciole sono indipendenti
        if ($has_fireflies) {
            $document->head[] = '<style>' . $this->getFirefliesCss() . '</style>';
            $document->foot[] = '<script>' . $this->getFirefliesJs() . '</script>';
        }
    }

    private function getNataleCss() {
        return <<<'CSS'
.avatar-festive-wrapper { position: relative; display: inline-block; }
.avatar-festive-wrapper > img.Avatar { display: block; }
.avatar-festive-hat { position: absolute; left: 50%; transform: translateX(-50%); pointer-events: none; z-index: 5; }
.PostUser-avatar + .avatar-festive-hat { transform: translateX(-150%); }
body, html { margin: 0 !important; padding: 0 !important; }
.App, .app, #app { margin-top: 0 !important; padding-top: 0 !important; }
#header.App-header, .App-header { overflow: visible !important; position: relative !important; margin-top: 0 !important; padding-top: 0 !important; }
#header.App-header::after, .App-header::after { content: "" !important; position: absolute !important; left: 0 !important; right: 0 !important; width: 100% !important; bottom: -25px !important; height: 40px !important; pointer-events: none !important; z-index: 9998 !important; background-image: url("data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%22400%22%20height%3D%2240%22%20viewBox%3D%220%200%20400%2040%22%3E%0A%20%20%3Cpath%20d%3D%22M0%2010%20Q%2050%2020%20100%2010%20T%20200%2010%20T%20300%2010%20T%20400%2010%22%20fill%3D%22none%22%20stroke%3D%22%232e7d32%22%20stroke-width%3D%224%22%20/%3E%0A%20%20%3Cpath%20d%3D%22M0%2018%20Q%2050%2028%20100%2018%20T%20200%2018%20T%20300%2018%20T%20400%2018%22%20fill%3D%22none%22%20stroke%3D%22%231b5e20%22%20stroke-width%3D%223%22%20/%3E%0A%20%20%3Ccircle%20cx%3D%2230%22%20cy%3D%2216%22%20r%3D%224%22%20fill%3D%22%23ff5252%22/%3E%0A%20%20%3Ccircle%20cx%3D%2270%22%20cy%3D%2214%22%20r%3D%224%22%20fill%3D%22%23ffeb3b%22/%3E%0A%20%20%3Ccircle%20cx%3D%22110%22%20cy%3D%2217%22%20r%3D%224%22%20fill%3D%22%23ff9800%22/%3E%0A%20%20%3Ccircle%20cx%3D%22150%22%20cy%3D%2215%22%20r%3D%224%22%20fill%3D%22%2340c4ff%22/%3E%0A%20%20%3Ccircle%20cx%3D%22190%22%20cy%3D%2216%22%20r%3D%224%22%20fill%3D%22%23ff5252%22/%3E%0A%20%20%3Ccircle%20cx%3D%22230%22%20cy%3D%2214%22%20r%3D%224%22%20fill%3D%22%23ffeb3b%22/%3E%0A%20%20%3Ccircle%20cx%3D%22270%22%20cy%3D%2217%22%20r%3D%224%22%20fill%3D%22%23ff9800%22/%3E%0A%20%20%3Ccircle%20cx%3D%22310%22%20cy%3D%2215%22%20r%3D%224%22%20fill%3D%22%2340c4ff%22/%3E%0A%20%20%3Ccircle%20cx%3D%22350%22%20cy%3D%2216%22%20r%3D%224%22%20fill%3D%22%23ff5252%22/%3E%0A%3C/svg%3E") !important; background-repeat: repeat-x !important; background-size: 200px 40px !important; display: block !important; }
#header, .App-header, .Header, #app, .App { overflow: visible !important; margin-top: 0 !important; padding-top: 0 !important; }
main.App-content { padding-top: 50px !important; margin-top: 0 !important; }
#snow { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 9999; }
#toggleSnow { position: fixed; bottom: 15px; left: 15px; z-index: 10000; padding: 5px 10px; font-size: 11px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1; border: 1px solid rgba(255, 255, 255, 0.18); border-radius: 14px; cursor: grab; background: rgba(255, 255, 255, 0.08); color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px) saturate(180%); -webkit-backdrop-filter: blur(12px) saturate(180%); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.1); transition: box-shadow 0.25s ease, background 0.25s ease, border-color 0.25s ease; display: flex; align-items: center; gap: 4px; resize: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; box-sizing: border-box; min-width: 60px; max-width: 80px; width: auto; height: auto; user-select: none; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; touch-action: none; }
#toggleSnow:active { cursor: grabbing; }
#toggleSnow:hover { background: rgba(255, 255, 255, 0.14); border-color: rgba(255, 255, 255, 0.28); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.15); }
#toggleSnow.light-theme { background: rgba(255, 255, 255, 0.55); border-color: rgba(0, 0, 0, 0.12); color: rgba(30, 30, 30, 0.95); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.9); }
#toggleSnow.light-theme:hover { background: rgba(255, 255, 255, 0.75); border-color: rgba(0, 0, 0, 0.2); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15), inset 0 1px 0 rgba(255, 255, 255, 1); }
.snow-icon { font-size: 13px; transition: all 0.3s ease; display: inline-block; line-height: 1; pointer-events: none; flex-shrink: 0; }
.snow-icon.active { color: #40c4ff; }
.snow-icon.inactive { color: #888888; opacity: 0.5; filter: grayscale(100%); }
#toggleSnow.light-theme .snow-icon.active { color: #0277bd; }
#toggleSnow.light-theme .snow-icon.inactive { color: #9e9e9e; opacity: 0.55; }
CSS;
    }

    private function getNataleAvatarJs() {
        return <<<'JS'
(function () {
  const AVATAR_SELECTOR = 'span.Avatar, img.Avatar';
  const svgns = 'http://www.w3.org/2000/svg';
  const MIN_AVATAR_SIZE = 32; 
  function createSvg(tag) { return document.createElementNS(svgns, tag); }
  function getAvatarSizePx(avatar) {
    const styles = getComputedStyle(avatar);
    const sizeVar = styles.getPropertyValue('--size').trim();
    if (sizeVar) { const n = parseFloat(sizeVar); if (!isNaN(n)) return n; }
    return avatar.offsetWidth || 36;
  }
  function addHatToAvatar(avatar) {
    if (avatar.dataset.festiveDecorated === '1') return;
    const size = getAvatarSizePx(avatar);
    if (size < MIN_AVATAR_SIZE) return;
    if (size === 0 || avatar.offsetParent === null) return;
    avatar.dataset.festiveDecorated = '1';
    const inComposer = avatar.closest('.Composer');
    let wrapper;
    if (inComposer) {
      wrapper = avatar.parentElement;
      if (wrapper) wrapper.style.position ||= 'relative';
    } else {
      const existingWrapper = avatar.closest('.avatar-festive-wrapper');
      if (existingWrapper) { wrapper = existingWrapper; } 
      else {
        wrapper = document.createElement('span');
        wrapper.className = 'avatar-festive-wrapper';
        wrapper.style.position = 'relative';
        wrapper.style.display = 'inline-block';
        avatar.parentNode.insertBefore(wrapper, avatar);
        wrapper.appendChild(avatar);
      }
    }
    if (!wrapper) return;
    const avatarRect = avatar.getBoundingClientRect();
    const wrapperRect = wrapper.getBoundingClientRect();
    const centerX = avatarRect.left + avatarRect.width / 2 - wrapperRect.left;
    const hats = ['santa', 'elf', 'reindeer'];
    const hat = hats[Math.floor(Math.random() * hats.length)];
    let svg = createSvg('svg');
    if (hat === 'reindeer') {
      svg.setAttribute('viewBox', '0 0 120 80');
      const paths = ['M40 70 C20 40, 35 20, 30 5', 'M30 25 C15 20, 15 10, 18 5', 'M35 35 C18 32, 18 22, 22 18', 'M80 70 C100 40, 85 20, 90 5', 'M90 25 C105 20, 105 10, 102 5', 'M85 35 C102 32, 102 22, 98 18'];
      paths.forEach(d => {
        const p = createSvg('path');
        p.setAttribute('d', d); p.setAttribute('stroke', '#8b4513'); p.setAttribute('stroke-width', '6'); p.setAttribute('fill', 'none');
        svg.appendChild(p);
      });
    } else {
      svg.setAttribute('viewBox', '0 0 100 60');
      const triangle = createSvg('path');
      triangle.setAttribute('d', 'M10 55 L60 5 L90 55 Z');
      triangle.setAttribute('fill', hat === 'santa' ? '#e53935' : '#3cb371');
      const brim = createSvg('rect');
      brim.setAttribute('x', '10'); brim.setAttribute('y', '45'); brim.setAttribute('width', '80'); brim.setAttribute('height', '15'); brim.setAttribute('rx', '7'); brim.setAttribute('fill', '#ffffff');
      svg.appendChild(triangle); svg.appendChild(brim);
    }
    svg.classList.add('avatar-festive-hat');
    wrapper.appendChild(svg);
    function updateSvgPosition() {
      const currentSize = getAvatarSizePx(avatar);
      if (currentSize < MIN_AVATAR_SIZE) { svg.style.display = 'none'; return; }
      svg.style.display = '';
      const rect = avatar.getBoundingClientRect();
      const wrapperRect = wrapper.getBoundingClientRect();
      const centerX = rect.left + rect.width / 2 - wrapperRect.left;
      const widthFactor = hat === 'reindeer' ? 0.95 : 0.68;
      const topFactor = hat === 'reindeer' ? -0.36 : -0.29;
      svg.style.width = currentSize * widthFactor + 'px';
      svg.style.top = currentSize * topFactor + 'px';
      svg.style.left = centerX + 'px';
      svg.style.transform = 'translateX(-50%)';
    }
    window.addEventListener('resize', updateSvgPosition);
    updateSvgPosition();
  }
  function decorateAllAvatars() { document.querySelectorAll(AVATAR_SELECTOR).forEach(addHatToAvatar); }
  function init() {
    decorateAllAvatars();
    new MutationObserver(decorateAllAvatars).observe(document.body, { childList: true, subtree: true });
  }
  if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init); } else { init(); }
})();
JS;
    }

    private function getSnowCss() {
        return <<<'CSS'
#snow { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 9999; }
#toggleSnow { position: fixed; bottom: 15px; left: 15px; z-index: 10000; padding: 5px 10px; font-size: 11px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1; border: 1px solid rgba(255, 255, 255, 0.18); border-radius: 14px; cursor: grab; background: rgba(255, 255, 255, 0.08); color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px) saturate(180%); -webkit-backdrop-filter: blur(12px) saturate(180%); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.1); transition: box-shadow 0.25s ease, background 0.25s ease, border-color 0.25s ease; display: flex; align-items: center; gap: 4px; resize: none; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; box-sizing: border-box; min-width: 60px; max-width: 80px; width: auto; height: auto; user-select: none; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; touch-action: none; }
#toggleSnow:active { cursor: grabbing; }
#toggleSnow:hover { background: rgba(255, 255, 255, 0.14); border-color: rgba(255, 255, 255, 0.28); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.15); }
#toggleSnow.light-theme { background: rgba(255, 255, 255, 0.55); border-color: rgba(0, 0, 0, 0.12); color: rgba(30, 30, 30, 0.95); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.9); }
#toggleSnow.light-theme:hover { background: rgba(255, 255, 255, 0.75); border-color: rgba(0, 0, 0, 0.2); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15), inset 0 1px 0 rgba(255, 255, 255, 1); }
.snow-icon { font-size: 13px; transition: all 0.3s ease; display: inline-block; line-height: 1; pointer-events: none; flex-shrink: 0; }
.snow-icon.active { color: #40c4ff; }
.snow-icon.inactive { color: #888888; opacity: 0.5; filter: grayscale(100%); }
#toggleSnow.light-theme .snow-icon.active { color: #0277bd; }
#toggleSnow.light-theme .snow-icon.inactive { color: #9e9e9e; opacity: 0.55; }
CSS;
    }

    private function getSnowJs() {
        return <<<'JS'
(function(){
  const SNOW_CONFIG = { initialFlakes: 140, flakesOnReenable: 140, maxFlakes: 180, lockToFooter: true, footerMargin: 15 };
  const disableKey = "snowDisabledUntil";
  const now = Date.now();
  const disabledUntil = localStorage.getItem(disableKey);
  let snowActive = true;
  let flakes = [];
  let currentThemeLight = false;
  if (disabledUntil && Number(disabledUntil) > now) { snowActive = false; }
  function isLightTheme() {
    if (document.body.classList.contains('App--light')) return true;
    if (document.documentElement.classList.contains('light')) return true;
    const rootStyle = getComputedStyle(document.documentElement);
    const bgVar = rootStyle.getPropertyValue('--body-bg') || rootStyle.getPropertyValue('--bg-color');
    if (bgVar && bgVar.includes('#')) {
      const hex = bgVar.trim().replace('#', '');
      if (hex.length >= 6) {
        const r = parseInt(hex.substr(0,2), 16); const g = parseInt(hex.substr(2,2), 16); const b = parseInt(hex.substr(4,2), 16);
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
    if (newIsLight) { btn.classList.add('light-theme'); } else { btn.classList.remove('light-theme'); }
    flakes.forEach(f => { f.opacity = newIsLight ? Math.max(f.opacity, 0.55) : Math.min(f.opacity, 0.7); });
  }
  currentThemeLight = isLightTheme();
  createToggleButton(snowActive, currentThemeLight);
  startSnow(currentThemeLight);
  if (window.matchMedia) {
    const themeQuery = window.matchMedia('(prefers-color-scheme: light)');
    themeQuery.addEventListener('change', updateThemeAppearance);
  }
  const bodyObserver = new MutationObserver((mutations) => {
    for (const mutation of mutations) {
      if (mutation.type === 'attributes' && mutation.attributeName === 'class') { updateThemeAppearance(); break; }
    }
  });
  bodyObserver.observe(document.body, { attributes: true });
  setInterval(updateThemeAppearance, 2000);
  function startSnow(isLight) {
    const canvas = document.getElementById('snow') || createCanvas();
    const ctx = canvas.getContext("2d");
    let width = canvas.width = window.innerWidth;
    let height = canvas.height = window.innerHeight;
    function random(min, max) { return min + Math.random() * (max - min); }
    function generateScale() {
      const r = Math.random();
      if (r < 0.6) return random(0.05, 0.3);
      if (r < 0.9) return random(0.3, 0.6);
      return random(0.6, 1);
    }
    if (snowActive) { addNewFlakes(SNOW_CONFIG.initialFlakes); }
    window.addEventListener('resize', () => { width = canvas.width = window.innerWidth; height = canvas.height = window.innerHeight; });
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
          if (snowActive) { flake.y = -flake.radius; flake.x = Math.random() * width; flake.fastFall = false; } 
          else { flake.y = height; flake.stopped = true; }
        }
        ctx.save();
        if (currentThemeLight) { ctx.shadowColor = 'rgba(20, 60, 100, 0.55)'; ctx.shadowBlur = 4; ctx.shadowOffsetX = 0; ctx.shadowOffsetY = 1; } 
        else { ctx.shadowColor = 'rgba(0, 0, 0, 0.4)'; ctx.shadowBlur = 2; ctx.shadowOffsetX = 0; ctx.shadowOffsetY = 1; }
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
  function createCanvas() { const canvas = document.createElement('canvas'); canvas.id = 'snow'; document.body.appendChild(canvas); return canvas; }
  function addNewFlakes(count) {
    const canvas = document.getElementById('snow');
    if (!canvas) return;
    const width = canvas.width; const height = canvas.height;
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
      flakes.push({ x: random(0, width), y: random(-height, 0), radius: radius, opacity: currentThemeLight ? random(0.55, 0.9) : random(0.3, 0.7), speed: random(0.5, 1.0) * scale, angle: random(0, Math.PI * 2), swing: random(-10, 10), yoyo: random(0.3, 1), stopped: false, fastFall: false });
    }
  }
  function createToggleButton(isOn, isLight) {
    const btn = document.createElement("button");
    btn.id = "toggleSnow";
    if (isLight) btn.classList.add('light-theme');
    btn.innerHTML = isOn ? '<span class="snow-icon active">❄️</span> ON' : '<span class="snow-icon inactive">❄️</span> OFF';
    document.body.appendChild(btn);
    let isDragging = false; let hasMoved = false; let startX, startY; let currentX = 0, currentY = 0; let initialX = 0, initialY = 0;
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
      isDragging = true; hasMoved = false;
      const clientX = e.touches ? e.touches[0].clientX : e.clientX;
      const clientY = e.touches ? e.touches[0].clientY : e.clientY;
      startX = clientX; startY = clientY; initialX = currentX; initialY = currentY;
      btn.style.transition = 'none';
    }
    function onMove(e) {
      if (!isDragging) return;
      const clientX = e.touches ? e.touches[0].clientX : e.clientX;
      const clientY = e.touches ? e.touches[0].clientY : e.clientY;
      const dx = clientX - startX;
      const dy = SNOW_CONFIG.lockToFooter ? 0 : (clientY - startY);
      let newX = initialX + dx; let newY = initialY + dy;
      btn.style.transform = `translate(${newX}px, ${newY}px)`;
      const rect = btn.getBoundingClientRect();
      const margin = 15;
      if (rect.left < margin) { newX += (margin - rect.left); } 
      else if (rect.right > window.innerWidth - margin) { newX -= (rect.right - (window.innerWidth - margin)); }
      if (SNOW_CONFIG.lockToFooter) { newY = 0; } 
      else {
        if (rect.top < margin) { newY += (margin - rect.top); } 
        else if (rect.bottom > window.innerHeight - margin) { newY -= (rect.bottom - (window.innerHeight - margin)); }
      }
      currentX = newX; currentY = newY;
      btn.style.transform = `translate(${currentX}px, ${currentY}px)`;
    }
    function onEnd() {
      if (!isDragging) return;
      isDragging = false;
      btn.style.transition = 'box-shadow 0.25s ease, background 0.25s ease, border-color 0.25s ease';
      localStorage.setItem('snowBtnPos', JSON.stringify({ x: currentX, y: currentY }));
    }
    btn.addEventListener('mousedown', onStart);
    window.addEventListener('mousemove', onMove);
    window.addEventListener('mouseup', onEnd);
    btn.addEventListener('touchstart', onStart, { passive: false });
    window.addEventListener('touchmove', onMove, { passive: false });
    window.addEventListener('touchend', onEnd);
    btn.addEventListener("click", (e) => {
      if (hasMoved) { e.preventDefault(); e.stopPropagation(); return; }
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
JS;
    }

    private function getFirefliesCss() {
        return <<<'CSS'
#magicToggle { position: fixed; bottom: 0; left: 80px; padding: 5px 8px; font-size: 10px; background: rgba(255,255,255,0.35); color: #000; border: 1px solid rgba(0,0,0,0.3); border-radius: 6px; cursor: pointer; z-index: 10000; backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); transition: transform 0.2s ease, background 0.2s ease; }
#magicContainer { position: fixed; inset: 0; pointer-events: none; overflow: hidden; z-index: 999; }
.firefly { position: absolute; border-radius: 50%; pointer-events: none; animation: float 5s infinite ease-in-out; filter: drop-shadow(0 0 4px currentColor); }
.firefly.hint { animation: hintFloat 2s ease-out forwards; }
@keyframes float { 0% { opacity: 0; transform: translate(0,0); } 10% { opacity: 1; } 50% { opacity: 0.7; transform: translate(-15px,10px); } 70% { opacity: 0.4; transform: translate(-30px,-10px); } 100% { opacity: 0; transform: translate(0,0); } }
@keyframes hintFloat { 0% { opacity: 0; transform: translateY(8px) scale(0.8); } 30% { opacity: 1; } 100% { opacity: 0; transform: translateY(-16px) scale(1); } }
@keyframes attentionPulse { 0% { box-shadow: 0 0 0 rgba(255,255,255,0); } 50% { box-shadow: 0 0 10px rgba(255,255,255,0.8); } 100% { box-shadow: 0 0 0 rgba(255,255,255,0); } }
#magicToggle.attention { animation: attentionPulse 0.5s ease-in-out; }
@keyframes idleGlow { from { box-shadow: 0 0 0 rgba(255,255,255,0.4); } to { box-shadow: 0 0 8px rgba(255,255,255,0.6); } }
@keyframes idleGlowFade { from { box-shadow: 0 0 8px rgba(255,255,255,0.6); } to { box-shadow: 0 0 0 rgba(255,255,255,0); } }
#magicToggle.idle { animation: idleGlow 0.5s ease-out forwards; }
#magicToggle.idle.fadeout { animation: idleGlowFade 0.5s ease-in forwards; }
CSS;
    }

    private function getFirefliesJs() {
        return <<<'JS'
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('magicContainer') || (() => { const c = document.createElement('div'); c.id = 'magicContainer'; document.body.appendChild(c); return c; })();
    const toggle = document.getElementById('magicToggle') || (() => { const b = document.createElement('button'); b.id = 'magicToggle'; document.body.appendChild(b); return b; })();
    const fireflies = [];
    const fireflyColors = ['rgba(120,200,255,1)', 'rgba(150,230,255,1)', 'rgba(255,255,255,1)'];
    const settings = { maxFireflies: 20, speed: 1, mode: 'always' };
    const stored = localStorage.getItem('fireflies_enabled');
    let magicEnabled;
    if (stored === null) { magicEnabled = true; localStorage.setItem('fireflies_enabled', 'true'); } 
    else { magicEnabled = stored === 'true'; }
    toggle.textContent = magicEnabled ? '✨ ON' : '☆ OFF';
    const firstSessionGlow = !sessionStorage.getItem('glow_shown');
    function createFirefly() {
        if (!magicEnabled || fireflies.length >= settings.maxFireflies) return;
        const f = document.createElement('div');
        f.className = 'firefly';
        const size = 3 + Math.random() * 2;
        f.style.width = f.style.height = size + 'px';
        f.style.left = Math.random() * window.innerWidth + 'px';
        f.style.top = Math.random() * window.innerHeight + 'px';
        f.style.background = f.style.color = fireflyColors[Math.floor(Math.random() * fireflyColors.length)];
        f.style.animationDuration = (4 + Math.random() * 4) / settings.speed + 's';
        container.appendChild(f);
        fireflies.push(f);
        setTimeout(() => { f.remove(); fireflies.splice(fireflies.indexOf(f), 1); }, parseFloat(f.style.animationDuration) * 1000);
    }
    function fadeOutFireflies() {
        fireflies.forEach((f, i) => {
            setTimeout(() => {
                f.style.opacity = 0;
                setTimeout(() => { if (fireflies.includes(f)) { f.remove(); fireflies.splice(fireflies.indexOf(f), 1); } }, 1000);
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
            if (elapsed >= duration) { toggle.classList.remove('attention'); clearInterval(blink); }
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
    if (settings.mode === 'first' && magicEnabled && !sessionStorage.getItem('firstSessionTimeout')) {
        sessionStorage.setItem('firstSessionTimeout', '1');
        setTimeout(() => {
            magicEnabled = false;
            localStorage.setItem('fireflies_enabled', 'false');
            toggle.textContent = '☆ OFF';
            fadeOutFireflies();
            attentionToToggleLong(30000, 500);
            spawnHintFirefly();
            if (firstSessionGlow) { sessionStorage.setItem('glow_shown', '1'); runIdleGlowOnce(); }
        }, 60000);
    }
    toggle.addEventListener('click', () => {
        magicEnabled = !magicEnabled;
        localStorage.setItem('fireflies_enabled', magicEnabled ? 'true' : 'false');
        toggle.textContent = magicEnabled ? '✨ ON' : '☆ OFF';
        if (!magicEnabled) fadeOutFireflies();
    });
    setInterval(createFirefly, 100);
});
JS;
    }
}
