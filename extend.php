<?php

use PeopleInside\FestiveDecorations\Listener\InjectAssets;
use Flarum\Extend;

return [
    // Carica il codice PHP nel frontend del forum
    (new Extend\Frontend('forum'))
        ->content(InjectAssets::class),
    
    // Carica esplicitamente il JavaScript nel pannello di amministrazione
    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js'),
    
    // Carica le traduzioni
    (new Extend\Locales(__DIR__ . '/locale')),
];
