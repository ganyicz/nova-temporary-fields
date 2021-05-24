# Make any Laravel Nova field temporary

This package adds support for creating fields you don't want to persist in your models.

```php

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Ganyicz\NovaTemporaryFields\HasTemporaryFields;

class User extends Resource
{
    use HasTemporaryFields;
    
    public function fields(Request $request)
    {
        return [
          Text::make('Non existent')->temporary()
        ];
    }
}
```

## Why?

This package is meant to be used in conjunction with my other package [Nova Callbacks](https://github.com/ganyicz/nova-callbacks).

Let's say you have a Product model with multiple prices depending on a currency defined in the Pricing model.

```php
class Product extends Model
{
  // ...
  
  public function pricing()
  {
    return $this->hasMany(Pricing::class);
  }
}
```

Now normally, you would use something like HasMany field on your Product resource. Although, this is not very intuitive for the admin. When he wants to update the product's pricing, he needs to go to the detail page and either create new pricing record or update an existing one. Not great. 

What if you just wanted to have a price field for every currency, right inside your edit page? Let's look at the solution.

```php
use Illuminate\Http\Request;
use Ganyicz\NovaCallbacks\HasCallbacks;
use Ganyicz\NovaCallbacks\HasTemporaryFields;

class User extends Resource
{
    use HasTemporaryFields,
        HasCallbacks;
    
    public function fields(Request $request)
    {
        return [
          // For the sake of simplicity, I'm not resolving the current price
        
          Number::make('USD')
            ->required()
            ->temporary(),
            
          Number::make('EUR')
            ->required()
            ->temporary(),
        ];
    }

    public static function afterSave(Request $request, $model)
    {
      $model->pricing()->firstOrNew(['currency' => 'USD'])->fill(['price' => $request->input('USD')])->save();
      $model->pricing()->firstOrNew(['currency' => 'EUR'])->fill(['price' => $request->input('EUR')])->save();
    }
}
```

Pretty elegant, right?

By defining a field as temporary, Nova won't try to fill the attribute to your model, avoiding the `Column not found` exception. Although you still have access to it inside your request.

The possibilties are endless.

## Installation

You can install the package via composer:

```bash
composer require ganyicz/nova-temporary-fields
```

## Usage

1. Apply `HasTemporaryFields` trait on your resource. 
2. Chain `temporary()` method on any field you don't want to fill to your model.

TIP: Apply the trait on your base Resource class inside your Nova folder so that temporary fields are available for you in every new resource.

## How does it work?

This package defines a `temporary()` macro on the base `Field` abstract class, which just sets a meta attribute `_temp` to true.
The `HasTemporaryFields` trait overrides `fillFields` method on your Resource and filters out any fields with this meta attribute.
