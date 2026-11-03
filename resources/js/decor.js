;(function(){
'use strict';
var c = window.DFEST;
if (!c || !c.enabled || !c.theme) { return; }
var t = c.theme;

function all(sel){ if(!sel){return [];} try{ return Array.prototype.slice.call(document.querySelectorAll(sel)); }catch(e){ return []; } }
function debounce(fn, wait){ var h; return function(){ clearTimeout(h); h = setTimeout(fn, wait); }; }

function headings(){
  all(c.headingSel).forEach(function(h){ h.classList.add('dfest-h1'); });
}

function logos(){
  all(c.logoSel).forEach(function(el){
    if (el.querySelector(':scope > .dfest-badge')) { return; }
    el.classList.add('dfest-logo');
    var b = document.createElement('span');
    b.className = 'dfest-badge dfest-badge--' + t.logoPos;
    b.setAttribute('aria-hidden', 'true');
    b.textContent = t.logoEmoji;
    el.appendChild(b);
    var img = el.querySelector('img');
    var h = (img ? img.getBoundingClientRect().height : 0) || el.getBoundingClientRect().height || 32;
    b.style.fontSize = Math.max(16, Math.min(44, h * 0.5)) + 'px';
  });
}

function fillGarland(wrap){
  var w = wrap.clientWidth || window.innerWidth;
  var n = Math.max(4, Math.floor(w / 54));
  if (wrap._n === n) { return; }
  wrap._n = n;
  wrap.textContent = '';
  for (var i = 0; i < n; i++) {
    var s = document.createElement('span');
    s.textContent = t.emojis[i % t.emojis.length];
    s.style.fontSize = (i % 2 ? '1rem' : '1.25rem');
    s.style.animationDelay = (-(i % 5) * 0.6) + 's';
    wrap.appendChild(s);
  }
}

function garland(){
  if (c.christmas) { return; } /* a Natale usa la ghirlanda SVG */
  var host = document.querySelector(c.menuHost || '#header');
  if (!host) { return; }
  var wrap = host.querySelector(':scope > .dfest-garland');
  if (!wrap) {
    if (window.getComputedStyle(host).position === 'static') { host.style.position = 'relative'; }
    wrap = document.createElement('div');
    wrap.className = 'dfest-garland';
    wrap.setAttribute('aria-hidden', 'true');
    host.appendChild(wrap);
  }
  fillGarland(wrap);
}

function items(){
  all(c.menuItemsSel).forEach(function(a){
    a.classList.add('dfest-item');
    var cur = a.classList.contains('active') || (a.parentElement && a.parentElement.classList.contains('active'));
    a.classList.toggle('dfest-current', !!cur);
    if (a.querySelector(':scope > .dfest-mark')) { return; }
    var m = document.createElement('span');
    m.className = 'dfest-mark';
    m.setAttribute('aria-hidden', 'true');
    m.textContent = t.mark;
    a.appendChild(m);
  });
}

function run(){
  if (c.heading) { headings(); }
  if (c.logo) { logos(); }
  if (c.menu) { garland(); items(); }
}
var later = debounce(run, 150);

function start(){
  run();
  /* Flarum è una SPA: riapplica le decorazioni dopo ogni cambio pagina */
  new MutationObserver(function(muts){
    for (var i = 0; i < muts.length; i++) {
      var n = muts[i].target;
      if (n.nodeType === 1 && !n.closest('#magicContainer,#snow,.dfest-garland')) { later(); return; }
    }
  }).observe(document.body, { childList: true, subtree: true });
  window.addEventListener('resize', later);
}

if (document.readyState !== 'loading') { start(); }
else { document.addEventListener('DOMContentLoaded', start); }
})();
