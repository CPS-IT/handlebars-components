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
* [Media service](Documentation/Services/MediaService.md)
* [Menu service](Documentation/Services/MenuService.md)

## Configuration

Several features can be enabled or disabled via extension configuration.
This includes, for example, helpers and template resolvers.

Please note that all available features are enabled by default!

## Contributing

Thank you for considering contributing to this extension! The following quality
assurance measures are in place and should be performed before each contribution.

### Preparation

Since some tests need to be executed with a full TYPO3 setup, make sure you
start the underlying DDEV project:

```bash
ddev start
ddev composer install
```

It is recommended to exclude the `.Build` directory in your IDE from indexing.

### Linting

```bash
ddev composer lint
# or
ddev composer lint:php
# or
ddev composer lint:typoscript
```

### Static code analysis

```bash
ddev composer sca
# or
ddev composer sca:php
```

Note: Sometimes it's necessary to increase the PHP memory limit. This can be done
by appending `-- --memory-limit=2G`, for example.

### Tests

Without coverage:

```bash
ddev composer test
# or
ddev composer test:functional
# or
ddev composer test:unit
```

With coverage report:

```bash
ddev composer test:ci
# or
ddev composer test:ci:functional
# or
ddev composer test:ci:unit
```

To merge coverage reports, run `ddev composer test:ci:merge`.

## License

This project is licensed under [GNU General Public License 2.0 (or later)](LICENSE.md).
