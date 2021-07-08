<?php

namespace Ganyicz\NovaTemporaryFields;

use Laravel\Nova\Http\Requests\NovaRequest;

trait HasTemporaryFields
{
    protected static function fillFields(NovaRequest $request, $model, $fields)
    {
        return parent::fillFields($request, $model, $fields->reject(function ($field) {
            return $field->meta['_temp'] ?? false;
        }));
    }
}
