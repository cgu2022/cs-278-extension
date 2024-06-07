<?php

use Flarum\Extend;
use CGU2022\CS278Extension\Api\GenerateSummaryController;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),
    (new Extend\Settings())
        ->serializeToForum('openaiApiKey', 'cgu2022.cs-278-extension.api_key'),
    (new Extend\Routes('api'))
        ->post('/generate-summary', 'generate-summary', GenerateSummaryController::class),
];
