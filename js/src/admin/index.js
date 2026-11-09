import app from 'flarum/admin/app';

const P = 'peopleinside-festive.';
const ID = 'peopleinside-festive-decorations';

const EVENTS = {
  natale: 'Natale (1 – 27 dic)',
  capodanno: 'Capodanno (28 dic – 6 gen)',
  halloween: 'Halloween (20 ott – 2 nov)',
  sanvalentino: 'San Valentino (7 – 14 feb)',
  pride: 'Pride (1 – 30 giu)',
  carnevale: 'Carnevale (giovedì – martedì grasso)',
  festadonna: 'Festa della donna (6 – 8 mar)',
  pasqua: 'Pasqua (settimana prima – lunedì dell’Angelo)',
  festamamma: 'Festa della mamma (settimana della 2ª domenica di maggio)',
  primavera: 'Stagione: Primavera (20 mar – 20 giu)',
  estate: 'Stagione: Estate (21 giu – 22 set)',
  autunno: 'Stagione: Autunno (23 set – 30 nov)',
  inverno: 'Stagione: Inverno (1 dic – 19 mar)',
  cuori: 'Tema di riserva: Fiori e cuori',
};

const THEMES = {
  auto: 'Automatico (in base alla data e agli eventi abilitati)',
  natale: 'Natale', capodanno: 'Capodanno', halloween: 'Halloween', carnevale: 'Carnevale',
  sanvalentino: 'San Valentino', festadonna: 'Festa della donna', pasqua: 'Pasqua', festamamma: 'Festa della mamma',
  pride: 'Pride', primavera: 'Primavera', estate: 'Estate', autunno: 'Autunno', inverno: 'Inverno', cuori: 'Fiori e cuori',
};

const DEFAULT_ON_EVENTS = [
  'natale',
  'halloween',
  'capodanno',
  'sanvalentino',
  'pride',
  'primavera',
  'estate',
  'autunno',
  'inverno',
];

app.initializers.add(ID, () => {
  const reg = app.registry || app.extensionData;
  const ext = reg.for(ID);
  const bool = (k, label, help, def = false) =>
    ext.registerSetting({ setting: P + k, type: 'boolean', label, help, default: def });
  const text = (k, label, help, def = '') =>
    ext.registerSetting({ setting: P + k, type: 'text', label, help, default: def });
  const num = (k, label, min, max, help, def = 0) =>
    ext.registerSetting({ setting: P + k, type: 'number', label, help, min, max, default: def });
  const select = (k, label, options, help, def = '') =>
    ext.registerSetting({ setting: P + k, type: 'select', label, options, help, default: def });

  bool('enabled', 'Decorazioni attive', 'Interruttore generale.', true);
  select('theme', 'Tema', THEMES, 'Con “Automatico” il tema dipende dalla data e dagli eventi spuntati qui sotto.', 'auto');
  text('timezone', 'Fuso orario', 'Es. Europe/Rome', 'Europe/Rome');

  Object.keys(EVENTS).forEach((k) =>
    bool('ev_' + k, EVENTS[k], undefined, DEFAULT_ON_EVENTS.includes(k))
  );

  bool('heading', 'Emoji ai lati del titolo', undefined, true);
  text('heading_sel', 'Selettore titolo', 'Predefinito: .Hero-title (titolo della welcome hero in homepage)', '.Hero-title');
  bool('logo', 'Emoji sul logo', undefined, true);
  text('logo_sel', 'Selettore logo', 'Predefinito: .Header-title a', '.Header-title a');
  bool('menu', 'Festone sotto l’header ed emoji sulle voci del menù', undefined, true);
  text('menu_host', 'Selettore contenitore festone', 'Predefinito: #header', '#header');
  text('menu_items_sel', 'Selettore voci del menù', 'Predefinito: .sideNav li > a', '.sideNav li > a');

  select('effect', 'Comportamento effetti', {
    auto: 'Automatico (secondo i periodi sotto)',
    christmas: 'Sempre decorazioni natalizie (neve + cappelli + ghirlanda)',
    snow: 'Sempre solo neve',
    fireflies: 'Sempre solo lucciole',
    none: 'Nessun effetto',
  }, undefined, 'auto');
  text('xmas_start', 'Periodo natalizio: dal (GG/MM)', 'Neve + cappelli sugli avatar + ghirlanda. Sostituisce la neve normale.', '01/12');
  text('xmas_end', 'Periodo natalizio: al (GG/MM)', undefined, '06/01');
  bool('xmas_hats', 'Cappelli natalizi sugli avatar', undefined, true);
  text('snow_start', 'Solo neve: dal (GG/MM)', 'Fuori dal periodo natalizio.', '25/10');
  text('snow_end', 'Solo neve: al (GG/MM)', undefined, '07/01');
  num('snow_count', 'Fiocchi iniziali', 1, 500, undefined, 140);
  num('snow_max', 'Fiocchi massimi', 1, 800, undefined, 180);
  text('ff_start', 'Lucciole: dal (GG/MM)', undefined, '08/01');
  text('ff_end', 'Lucciole: al (GG/MM)', undefined, '24/10');
  num('ff_count', 'Numero di lucciole', 1, 100, undefined, 20);
  num('ff_speed', 'Velocità lucciole', 1, 10, undefined, 3);
  select('ff_mode', 'Lucciole: modalità', {
    always: 'Sempre visibili',
    first: 'Solo i primi 60 s della sessione, poi il pulsante lampeggia',
  }, undefined, 'always');
  bool('fx_toggle', 'Mostra i pulsanti ON/OFF di neve e lucciole', undefined, true);

  ext.registerSetting({
    setting: P + 'custom_css',
    type: 'textarea',
    label: 'CSS personalizzato',
    help: 'Aggiunto in coda agli stili del plugin.',
  });
});
