# Decorazioni Festive per Flarum 2.x

Porting del plugin WordPress "Decorazioni Festive".

## Installazione
Copia la cartella in `extensions/festive-decorations` (nella root di Flarum), poi:

    composer require peopleinside/festive-decorations
    php flarum cache:clear

Abilita l'estensione dal pannello admin e configurala dalla sua pagina impostazioni.

## Come funzionano gli effetti (modalità Automatico)
1. Periodo natalizio (default 01/12 – 06/01): neve + cappelli sugli avatar + ghirlanda SVG nell'header (il tuo script natalizio). Sostituisce la neve normale.
2. Fuori dal Natale, nel periodo neve (default 25/10 – 07/01): solo neve (CANVAS NEVE).
3. Periodo lucciole (default 08/01 – 24/10): script lucciole.
Titolo, logo, menù e festone emoji seguono il tema del giorno (festività/stagioni), come nel plugin WordPress.

## Se qualcosa non compare
Adatta i selettori CSS dalle impostazioni (titolo, logo, voci menù, contenitore festone).

## Ricompilare i bundle JS (opzionale)
    cd js && npm install && npm run build

## Novità versione 1.0.5
- **Performance e caching**: eliminato il caricamento sincrono su disco (`file_get_contents`) ad ogni richiesta per gli asset statici CSS e JS.
- **Integrazione nativa Flarum 2.x**: gli stili statici sono ora compilati nel bundle CSS del forum (`resources/less/forum.less`) e gli script nel bundle JS (`js/dist/forum.js`), sfruttando la cache HTTP del browser e dei CDN.
- **Configurazione dinamica ultra-leggera**: solo l'oggetto `window.DFEST` e le variabili CSS dinamiche del tema corrente vengono iniettati nell'head HTML.
