<?php

namespace Ganyicz\NovaTemporaryFields;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Fields\Field;

class TemporaryFieldsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Field::macro('temporary', function () {
            return $this->withMeta(['_temp' => true]);
        });
    }
}