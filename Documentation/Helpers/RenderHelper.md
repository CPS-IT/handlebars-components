# [`render` helper](../../Classes/Renderer/Helper/RenderHelper.php)

## Reference

<https://fractal.build/guide/core-concepts/view-templates.html#render>

## Description

This helper implements the `render` helper known from Fractal. It is used to render
templates based on a specific context. Instead of a default context an alternative
context can be passed, additionally both contexts can also be merged.

To define a default context, the renderer must be passed an array whose key matches
the name of the template to be rendered.

### Rendering uncached templates

Version 0.4.0 introduced the ability to render templates uncached when using the
`render` helper. This can be useful if only single page components need to be
uncached whereas everything else is cached, for example to show active frontend
sessions. In such cases, only the generic parts of the page are cacheable, whereas
dynamic parts must be rendered on request.

See example 4 for further instructions.

## Examples

### Example 1: Default context only

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

### Example 2: Usage of either default context or custom context

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

### Example 3: Merge default context with custom context

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

### Example 4: Render template uncached

In order to render templates uncached, add `uncached=true`:

```handlebars
{{! modules/menu/menu.hbs}}

<ul class="menu">
    {{render '@menu-items' menuItemsData uncached=true}}
</ul>
```

This registers the rendering of this specific template as non-cacheable
resulting in a **marker** such as `<!--INT_SCRIPT.47ther64-->`. The marker is
then resolved on request delivering the final (uncached) template content.

For this, an appropriate **data processor** needs to be **written and referenced**.
The data processor is then responsible to deliver the uncached template content
and is requested after final template rendering by TSFE (see
`TypoScriptFrontendController::INTincScript()`). You need to reference the data
processor in your template context:

```php
# MenuPresenter.php

$this->renderer->render('@menu', [
    'menuItemsData' => [
        '_processor' => MenuProcessor::class,
    ],
]);
```

In the referenced data processor you can then resolve the template variables and
render the final template:

```php
# MenuProcessor.php

use Fr\Typo3Handlebars\DataProcessing\AbstractDataProcessor;
use Cpsit\Typo3HandlebarsComponents\DataProcessing\DefaultContextAwareConfigurationTrait;
use Cpsit\Typo3HandlebarsComponents\DataProcessing\TemplatePathAwareConfigurationTrait;

/**
 * @property MenuProvider $provider;
 * @property MenuPresenter $presenter;
 */
class MenuProcessor extends AbstractDataProcessor
{
    use TemplatePathAwareConfigurationTrait;
    use DefaultContextAwareConfigurationTrait;

    protected function render(): string
    {
        $templatePath = $this->getTemplatePathFromConfiguration();
        $context = $this->getDefaultContextFromConfiguration();

        // Use the provider to resolve the menu items or do similar things that
        // require the template to be rendered uncached.
        $data = $this->provider->get($context);
        $data->setTemplatePath($templatePath);

        return $this->presenter->present($data);
    }
}
```
