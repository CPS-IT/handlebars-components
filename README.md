[![Pipeline](https://gitlab.321.works/typo3/handlebars-components/badges/develop/pipeline.svg)](https://gitlab.321.works/typo3/handlebars-components/-/pipelines)
[![Coverage](https://gitlab.321.works/typo3/handlebars-components/badges/develop/coverage.svg)](https://gitlab.321.works/typo3/handlebars-components/-/pipelines)
[![License](https://badgen.net/badge/license/GPL-2.0-or-later)](LICENSE.md)

# TYPO3 extension `handlebars_components`

> Additional components for the TYPO3 CMS extension "handlebars"

## Installation

First, register the Git repository in your `composer.json`:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "git@gitlab.321.works:typo3/handlebars-components.git"
    }
  ]
}
```

Now require the package with Composer:

```bash
composer require fr/typo3-handlebars-components
```

## Shipped components

### [`FlatTemplateResolver`](Classes/Renderer/Template/FlatTemplateResolver.php)

This resolver extends the original `HandlebarsTemplateResolver`, but flattens all
given template root paths. That means, all files within those directories are
stored as flat list and accessed accordingly.

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

### Handlebars Layouts

This is an implementation of [`handlebars-layouts`](https://github.com/shannonmoeller/handlebars-layouts)
that that allows to define layouts that can be extended with custom content.

The following Handlebars helpers currently exist:

| Helper | Declaring class | Reference |
| ------ | --------------- | --------- |
| `extend` | [`ExtendHelper`](Classes/Renderer/Helper/ExtendHelper.php) | [Reference][1] |
| `block` | [`BlockHelper`](Classes/Renderer/Helper/BlockHelper.php) | [Reference][2] |
| `content` | [`ContentHelper`](Classes/Renderer/Helper/ContentHelper.php) | [Reference][3] |

[1]: https://github.com/shannonmoeller/handlebars-layouts#extend-partial-context-keyvalue-
[2]: https://github.com/shannonmoeller/handlebars-layouts#block-name
[3]: https://github.com/shannonmoeller/handlebars-layouts#content-name-modeappendprependreplace

## License

This project is licensed under [GNU General Public License 2.0 (or later)](LICENSE.md).
