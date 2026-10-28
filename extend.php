<?php

use PeopleInside\FestiveDecorations\Listener\InjectAssets;
use Flarum\Extend;

return [
    (new Extend\Frontend('forum'))
        ->content(InjectAssets::class),
    
    (new Extend\Locales(__DIR__ . '/locale')),
];
