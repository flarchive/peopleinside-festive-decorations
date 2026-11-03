;(function () {
  var c = window.DFEST;
  if (!c || !c.enabled || !c.christmas || !c.hats) {
    return;
  }
  const AVATAR_SELECTOR = 'span.Avatar, img.Avatar';
  const svgns = 'http://www.w3.org/2000/svg';
  const MIN_AVATAR_SIZE = 32;

  function createSvg(tag) {
    return document.createElementNS(svgns, tag);
  }

  function getAvatarSizePx(avatar) {
    const styles = getComputedStyle(avatar);
    const sizeVar = styles.getPropertyValue('--size').trim();
    if (sizeVar) {
      const n = parseFloat(sizeVar);
      if (!isNaN(n)) return n;
    }
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
      if (existingWrapper) {
        wrapper = existingWrapper;
      } else {
        wrapper = document.createElement('span');
        wrapper.className = 'avatar-festive-wrapper';
        wrapper.style.position = 'relative';
        wrapper.style.display = 'inline-block';
        avatar.parentNode.insertBefore(wrapper, avatar);
        wrapper.appendChild(avatar);
      }
    }

    if (!wrapper) return;

    const hats = ['santa', 'elf', 'reindeer'];
    const hat = hats[Math.floor(Math.random() * hats.length)];

    let svg = createSvg('svg');

    if (hat === 'reindeer') {
      svg.setAttribute('viewBox', '0 0 120 80');
      const paths = [
        'M40 70 C20 40, 35 20, 30 5',
        'M30 25 C15 20, 15 10, 18 5',
        'M35 35 C18 32, 18 22, 22 18',
        'M80 70 C100 40, 85 20, 90 5',
        'M90 25 C105 20, 105 10, 102 5',
        'M85 35 C102 32, 102 22, 98 18'
      ];
      paths.forEach(d => {
        const p = createSvg('path');
        p.setAttribute('d', d);
        p.setAttribute('stroke', '#8b4513');
        p.setAttribute('stroke-width', '6');
        p.setAttribute('fill', 'none');
        svg.appendChild(p);
      });
    } else {
      svg.setAttribute('viewBox', '0 0 100 60');
      const triangle = createSvg('path');
      triangle.setAttribute('d', 'M10 55 L60 5 L90 55 Z');
      triangle.setAttribute('fill', hat === 'santa' ? '#e53935' : '#3cb371');
      const brim = createSvg('rect');
      brim.setAttribute('x', '10');
      brim.setAttribute('y', '45');
      brim.setAttribute('width', '80');
      brim.setAttribute('height', '15');
      brim.setAttribute('rx', '7');
      brim.setAttribute('fill', '#ffffff');
      svg.appendChild(triangle);
      svg.appendChild(brim);
    }

    svg.classList.add('avatar-festive-hat');
    wrapper.appendChild(svg);

    function updateSvgPosition() {
      const currentSize = getAvatarSizePx(avatar);
      if (currentSize < MIN_AVATAR_SIZE) {
        svg.style.display = 'none';
        return;
      }
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

  function decorateAllAvatars() {
    document.querySelectorAll(AVATAR_SELECTOR).forEach(addHatToAvatar);
  }

  function init() {
    decorateAllAvatars();
    var pending = false;
    new MutationObserver(function (muts) {
      for (var i = 0; i < muts.length; i++) {
        var n = muts[i].target;
        if (n.nodeType === 1 && n.closest && n.closest('#magicContainer,#snow')) continue;
        if (pending) return;
        pending = true;
        requestAnimationFrame(function () { pending = false; decorateAllAvatars(); });
        return;
      }
    }).observe(document.body, { childList: true, subtree: true });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
