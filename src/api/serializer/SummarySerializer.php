<?php

namespace CGU2022\CS278Extension\Api\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;

class SummarySerializer extends AbstractSerializer
{
    protected $type = 'summary';

    protected function getDefaultAttributes($model)
    {
        return [
            'summary' => $model->summary,
        ];
    }
}
