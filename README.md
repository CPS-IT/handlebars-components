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

> Reference: https://fractal.build/guide/core-concepts/naming.html

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

### [`RenderHelper`](Classes/Renderer/Helper/RenderHelper.php)

> Reference: https://fractal.build/guide/core-concepts/view-templates.html#render

This helper implements the `render` helper known from Fractal. It is used to render
templates based on a specific context. Instead of a default context an alternative
context can be passed, additionally both contexts can also be merged.

To define a default context, the renderer must be passed an array whose key matches
the name of the template to be rendered.

#### Example 1: Default context only

```php
$renderData = [
    // Default context for the template modules/message/message.hbs (see below)
    '@message' => [
        'class' => 'message--alert',
        'title' => 'Attention',
        'message' => 'Hello world, you are in danger!',
    ],
];
$this->renderer->render('pages/default/default', $renderData);
```

Templates:

```handlebars
{{! pages/default/default.hbs}}

<div class="page">
    {{render '@message'}}
</div>
```

```handlebars
{{! modules/message/message.hbs}}

<div class="message {{ class }}">
    {{#if title}}
        <strong>{{ title }}</strong>
    {{/if}}
    {{#if message}}
        <p>{{ message }}</p>
    {{/if}}
</div>
```

This produces the following output:

```html
<div class="page">
    <div class="message message--alert">
        <strong>Attention</strong>
        <p>Hello world, you are in danger!</p>
    </div>
</div>
```

#### Example 2: Usage of either default context or custom context

```php
$renderData = [
    // Default context for the template modules/menu/menu-items.hbs (see below)
    '@menu-items' => [
        'items' => [
            [
                'class' => 'menu-item--home',
                'title' => 'Home',
            ],
            [
                'class' => 'menu-item--services',
                'title' => 'Services',
                'subItems' => [
                    [
                        'title' => 'Service A',
                    ],
                    [
                        'title' => 'Service B',
                    ],
                ],
            ],
        ],
    ],
];
$this->renderer->render('modules/menu/menu', $renderData);
```

Templates:

```handlebars
{{! modules/menu/menu.hbs}}

<ul class="menu">
    {{render '@menu-items'}}
</ul>
```

```handlebars
{{! modules/menu/menu-items.hbs}}

{{#each items}}
    <li class="menu-item {{ class }}">
        <span>{{ title }}</span>
        {{#if subItems}}
            <ul class="submenu">
                {{#each subItems}}
                    {{render '@menu-items' subItems}}
                {{/each}}
            </ul>
        {{/if}}
    </li>
{{/each}}
```

This produces the following output:

```html
<ul class="menu">
    <li class="menu-item menu-item--home">
        <span>Home</span>
    </li>
    <li class="menu-item menu-item--services">
        <span>Services</span>
        <ul class="submenu">
            <li class="menu-item">
                Service A
            </li>
            <li class="menu-item">
                Service B
            </li>
        </ul>
    </li>
</ul>
```

#### Example 3: Merge default context with custom context

Contexts can be merged by using the additional parameter `merge=true`.

```php
$renderData = [
    // Default context for the template modules/message/message.hbs (see below)
    '@message' => [
        'class' => 'message--default',
    ],
    // Custom render data
    'messages' => [
        [
            'title' => 'Welcome',
            'message' => 'Hello world, it\'s nice to see you!',
        ],
        [
            'class' => 'message--alert',
            'title' => 'Attention',
            'message' => 'Hello world, you are in danger!',
        ],
    ],
];
$this->renderer->render('pages/default/default', $renderData);
```

Templates:

```handlebars
{{! pages/default/default.hbs}}

<div class="page">
    {{#each messages}}
        {{render '@message' this merge=true}}
    {{/each}}
</div>
```

```handlebars
{{! modules/message/message.hbs}}

<div class="message {{ class }}">
    {{#if title}}
        <strong>{{ title }}</strong>
    {{/if}}
    {{#if message}}
        <p>{{ message }}</p>
    {{/if}}
</div>
```

This produces the following output:

```html
<div class="page">
    <div class="message message--default">
        <strong>Welcome</strong>
        <p>Hello world, it's nice to see you!</p>
    </div>
    <div class="message message--alert">
        <strong>Attention</strong>
        <p>Hello world, you are in danger!</p>
    </div>
</div>
```

### Handlebars Layouts

> Reference: https://github.com/shannonmoeller/handlebars-layouts

This is an implementation of [`handlebars-layouts`](https://github.com/shannonmoeller/handlebars-layouts)
that that allows to define layouts that can be extended with custom content.

The following Handlebars helpers currently exist:

| Helper    | Declaring class                                              | Reference      |
| --------- | ------------------------------------------------------------ | -------------- |
| `extend`  | [`ExtendHelper`](Classes/Renderer/Helper/ExtendHelper.php)   | [Reference][1] |
| `block`   | [`BlockHelper`](Classes/Renderer/Helper/BlockHelper.php)     | [Reference][2] |
| `content` | [`ContentHelper`](Classes/Renderer/Helper/ContentHelper.php) | [Reference][3] |

[1]: https://github.com/shannonmoeller/handlebars-layouts#extend-partial-context-keyvalue-
[2]: https://github.com/shannonmoeller/handlebars-layouts#block-name
[3]: https://github.com/shannonmoeller/handlebars-layouts#content-name-modeappendprependreplace

## License

This project is licensed under [GNU General Public License 2.0 (or later)](LICENSE.md).
