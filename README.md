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

## Features

### Handlebars components

* [Page rendering](Documentation/Components/PageRendering.md)

### Handlebars helpers

* [`block`, `content` and `extend` helpers](Documentation/Helpers/HandlebarsLayouts.md)
  from _handlebars-layouts_
* [`render` helper](Documentation/Helpers/RenderHelper.md) from _Fractal_

### Handlebars template resolvers

* [Flat template resolver](Documentation/TemplateResolvers/FlatTemplateResolver.md) from _Fractal_

### Services

* [Configuration service](Documentation/Services/ConfigurationService.md)
* [Menu service](Documentation/Services/MenuService.md)

## License

This project is licensed under [GNU General Public License 2.0 (or later)](LICENSE.md).
