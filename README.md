# Nova Panels

A custom panels package for those Nova resources with lots of relationships and/or fields.

## Status

[![StyleCI](https://github.styleci.io/repos/352946226/shield?branch=main)](https://github.styleci.io/repos/352946226) [![Codacy Badge](https://api.codacy.com/project/badge/Grade/94afbef5635c439bb9da4284ff2f3a7f)](https://www.codacy.com/gh/skore/nova-panels/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=skore/nova-panels&amp;utm_campaign=Badge_Grade)

## Getting started

Grab the package using `Composer`:

```
composer require skorelabs/nova-panels
```

### Usage

Add this to your resource's fields method:

```php
TabbedPanel::make(__('TabbedPanel'), [
  'FirstTab' => [
    Text::make(__('Name'), 'name'),
    
    BelongsTo::make(__('Author'), 'author', User::class),
  ]
]),
```

And remember to import the class `SkoreLabs\NovaPanels\TabbedPanel`.

_More coming soon, remember that this is a pre-released package..._

## Support

This and all of our Laravel packages follows as much as possibly can the LTS support of Laravel.

Read more: https://laravel.com/docs/master/releases#support-policy

## Credits

- [@eminiarts](https://github.com/eminiarts) packages: [nova-relationship-selector](https://github.com/eminiarts/nova-relationship-selector) & [nova-tabs](https://github.com/eminiarts/nova-tabs)
- Ruben Robles ([@d8vjork](https://github.com/d8vjork))
- Skore ([https://www.getskore.com/](https://www.getskore.com/))
- [And all the contributors](https://github.com/skore-labs/laravel-status/graphs/contributors)
