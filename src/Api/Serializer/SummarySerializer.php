<?php

namespace CGU2022\CS278Extension\Api\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;

class SummarySerializer extends AbstractSerializer
{
    // Define the type of resource this serializer handles.
    // This type corresponds to the JSON:API resource type.
    protected $type = 'summary';

    // Method to specify the default attributes that should be serialized for the model.
    // The $model parameter is the instance of the model being serialized.
    protected function getDefaultAttributes($model)
    {
        // Return an associative array of attributes.
        // The key is the attribute name and the value is the attribute value.
        return [
            'summary' => $model->summary, // Serialize the 'summary' attribute of the model.
        ];
    }
}
