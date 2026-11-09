import app from 'flarum/forum/app';
import { initDecor } from './decor';
import { initSnow } from './snow';
import { initHats } from './hats';
import { initFireflies } from './fireflies';

app.initializers.add('peopleinside-festive-decorations', () => {
  const c = window.DFEST;
  if (!c || !c.enabled) return;

  if (c.christmas) {
    document.documentElement.classList.add('dfest-xmas');
  }

  if (c.theme) {
    initDecor(c);
  }

  if (c.snow) {
    initSnow(c.snow);
  }

  if (c.hats) {
    initHats();
  }

  if (c.ff) {
    initFireflies(c.ff);
  }
});
