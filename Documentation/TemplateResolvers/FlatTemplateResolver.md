# [Flat template resolver](../../Classes/Renderer/Template/FlatTemplateResolver.php)

## Reference

<https://fractal.build/guide/core-concepts/naming.html>

## Description

This resolver extends the original `HandlebarsTemplateResolver`, but flattens all
given template root paths. That means, all files within those directories are
stored as flat list and accessed accordingly.

## Example

Example tree:

```
├── 010_fundamentals
│   ├── colors
│   │   └── colors.hbs
│   └── icons
│       └── icons.hbs
└── modules
    ├── 000_alert
    │   └── alert.hbs
    └── 010_hero
        └── hero.hbs
```

Resulting template list:

* `alert`
* `colors`
* `hero`
* `icons`

**Note: Only templates that are prefixed by `@` are accessed using the flat map.
All other templates paths are processed as-is.**

Example usage:

```php
$templateRootPaths = [
    // ...
];
$templateResolver = new \Fr\Typo3HandlebarsComponents\Renderer\Template\FlatTemplateResolver($templateRootPaths);

$templatePath = $templateResolver->resolveTemplatePath('@alert'); // results in modules/000_alert/alert.hbs
$templatePath = $templateResolver->resolveTemplatePath('modules/000_alert/alert'); // same as above
$templatePath = $templateResolver->resolveTemplatePath('alert'); // will throw an exception
```
