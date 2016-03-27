<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Serializer
    |--------------------------------------------------------------------------
    |
    | The default serializer to be used when performing a transformation. It
    | may be left empty to use Fractal's default one. This can either be a
    | string or a League\Fractal\Serializer\SerializerAbstract subclass.
    |
    | Available serializers:
    |  - DataArraySerializer (default)
    |  - ArraySerializer
    |  - JsonApiSerializer
    |
    */
    'default_serializer' => 'League\Fractal\Serializer\ArraySerializer',
];