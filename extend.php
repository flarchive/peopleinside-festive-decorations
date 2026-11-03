<?php

use Flarum\Extend;
use PeopleInside\FestiveDecorations\Content;
use PeopleInside\FestiveDecorations\Decor;

// Caricamento esplicito: funziona anche se l'autoload di Composer non è aggiornato.
require_once __DIR__ . '/src/Decor.php';
require_once __DIR__ . '/src/Content.php';

$settings = new Extend\Settings();
foreach (Decor::defaults() as $key => $value) {
    $settings->default(Decor::P . $key, $value);
}

$frontend = (new Extend\Frontend('forum'))
    ->css(__DIR__ . '/resources/less/forum.less')
    ->content(Content::class);

// Se il file js/dist/forum.js esiste (compilato da webpack), lo usa.
// Altrimenti carica direttamente i file da resources/js/: in questo modo,
// se l'estensione viene installata via composer senza il file compilato in git,
// "php flarum cache:clear" non fallirà mai con "File not found at path .../forum.js".
if (is_file(__DIR__ . '/js/dist/forum.js')) {
    $frontend->js(__DIR__ . '/js/dist/forum.js');
} else {
    $frontend
        ->js(__DIR__ . '/resources/js/decor.js')
        ->js(__DIR__ . '/resources/js/snow.js')
        ->js(__DIR__ . '/resources/js/hats.js')
        ->js(__DIR__ . '/resources/js/fireflies.js');
}

return [
    $frontend,
    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js'),
    $settings,
];
