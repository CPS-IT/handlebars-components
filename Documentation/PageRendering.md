# Component: Page rendering

## Description

This component provides an abstract page rendering that can
be used to output various page layouts. It includes data
providing as well as an abstract
[`PagePresenter`](../Classes/Presenter/AbstractPagePresenter.php)
with which basic page rendering is possible.

In addition, interfaces for different data models and
associated factories are provided, which can be implemented
as desired.

## Prerequisites

The page rendering component assumes that a template called
`cms.hbs` exists in your template root paths. It should
contain the following content and does not have to be used or
referenced by other handlebars files. The template files allows for
a more convenient way to render multiple layouts.

```handlebars
{{#extend templateName}}
    {{#content 'mainContent'}}
        {{{ mainContent }}}
    {{/content}}
{{/extend}}
```

If you want to use a different rendering mechanism, you can
simply use your own implementation instead of
the `AbstractPagePresenter`.

## Example

### Custom `PagePresenter`

Create your specific [`PagePresenter`](../Classes/Presenter/AbstractPagePresenter.php)
first:

```php
# Classes/Presenter/PagePresenter.php

namespace Venor\Extension\Presenter;

use Fr\Typo3HandlebarsComponents\Data\Response\PageProviderResponse;
use Fr\Typo3HandlebarsComponents\Presenter\AbstractPagePresenter;

class PagePresenter extends AbstractPagePresenter
{
    protected function determineTemplateName(PageProviderResponse $data): string
    {
        // TODO: Implement logic to define the template to use, based on the PageProviderResponse passed

        // Example:
        // switch ($layout = $data->getPage()->getLayout())
        // {
        //     case 'pagets__1col':
        //         return '@1col';
        //     case 'pagets__2col':
        //         return '@2col';
        //     default:
        //         throw new \RuntimeException(sprintf('The page layout "%s" is not supported.', $layout), 1630679678);
        // }
    }

    protected function getAdditionalRenderData(PageProviderResponse $data): array
    {
        // TODO: Provide additional data that is passed to the renderer (optional)
    }
}
```

### Register data processor

Now register the
[`PageProcessor`](../Classes/DataProcessing/PageProcessor.php)
in order to make it available, e.g. within TypoScript:

```yaml
# Configuration/Services.yaml

services:
  Fr\Typo3HandlebarsComponents\DataProcessing\PageProcessor:
    tags: ['handlebars.processor']
    calls:
      - setPresenter: ['@Vendor\Extension\Presenter\PagePresenter']
```

### `PageHeader` and `PageFooter`

If your templates use a page header or page footer, you need to
implement appropriate models and factories that create concrete
[`PageHeader`](../Classes/Domain/Model/Page/PageHeaderInterface.php)
and [`PageFooter`](../Classes/Domain/Model/Page/PageFooterInterface.php)
objects.

#### Create models

First create the custom `PageHeader` and/or `PageFooter` models:

```php
# Classes/Domain/Model/Page/PageHeader.php

namespace Vendor\Extension\Domain\Model\Page;

use Fr\Typo3HandlebarsComponents\Domain\Model\Page\PageHeaderInterface;

class PageHeader implements PageHeaderInterface
{
    // TODO: Implement properties, getters and setters
}
```

```php
# Classes/Domain/Model/Page/PageFooter.php

namespace Vendor\Extension\Domain\Model\Page;

use Fr\Typo3HandlebarsComponents\Domain\Model\Page\PageFooterInterface;

class PageFooter implements PageFooterInterface
{
    // TODO: Implement properties, getters and setters
}
```

#### Create factories

Now create the associated factories:

```php
# Classes/Domain/Factory/Page/PageHeaderFactory.php

namespace Vendor\Extension\Domain\Factory\Page;

use Fr\Typo3HandlebarsComponents\Domain\Factory\Page\PageHeaderFactoryInterface;
use Fr\Typo3HandlebarsComponents\Domain\Model\Page;
use Vendor\Extension\Domain\Model\Page\PageHeader;

class PageHeaderFactory implements PageHeaderFactoryInterface
{
    public function get(Page $page): PageHeader
    {
        $pageHeader = new PageHeader();

        // TODO: Fill $pageHeader with required properties

        return $pageHeader;
    }
}
```

```php
# Classes/Domain/Factory/Page/PageFooterFactory.php

namespace Vendor\Extension\Domain\Factory\Page;

use Fr\Typo3HandlebarsComponents\Domain\Factory\Page\PageFooterFactoryInterface;
use Fr\Typo3HandlebarsComponents\Domain\Model\Page;
use Vendor\Extension\Domain\Model\Page\PageFooter;

class PageFooterFactory implements PageFooterFactoryInterface
{
    public function get(Page $page): PageFooter
    {
        $pageFooter = new PageFooter();

        // TODO: Fill $pageFooter with required properties

        return $pageFooter;
    }
}
```

#### Register factories

Last but not least you need to register the factories in the
`Services.yaml` for the
[`PageProvider`](../Classes/Data/PageProvider.php):

```yaml
# Configuration/Services.yaml

services:
  Fr\Typo3HandlebarsComponents\Data\PageProvider:
    arguments:
      $pageHeaderFactory: '@Vendor\Extension\Domain\Factory\Page\PageHeaderFactory'
      $pageFooterFactory: '@Vendor\Extension\Domain\Factory\Page\PageFooterFactory'
```


### Page content rendering

Since the rendering of page content varies from system to system,
a specific implementation is also needed for this. The
[`PageContentRendererInterface`](../Classes/Renderer/Component/Page/PageContentRendererInterface.php)
interface must be implemented for this purpose.

Note: The raw TypoScript configuration is passed to the `render`
method as second parameter `$configuration`.

```php
# Classes/Renderer/Component/Page/PageContentRenderer.php

namespace Vendor\Extension\Renderer\Component\Page;

use Fr\Typo3Handlebars\ContentObjectRendererAwareInterface;
use Fr\Typo3Handlebars\Traits\ContentObjectRendererAwareTrait;
use Fr\Typo3HandlebarsComponents\Domain\Model\Page;
use Fr\Typo3HandlebarsComponents\Renderer\Component\Page\PageContentRendererInterface;

class PageContentRenderer implements PageContentRendererInterface, ContentObjectRendererAwareInterface
{
    use ContentObjectRendererAwareTrait;

    public function render(Page $page, array $configuration): string
    {
        $this->assertContentObjectRendererIsAvailable();

        if (!isset($configuration['userFunc.'])) {
            return '';
        }

        return $this->contentObjectRenderer->cObjGetSingle(
            $configuration['userFunc.']['content'],
            $configuration['userFunc.']['content.']
        );
    }
}
```

Lastly, you need to register this implementation in your `Services.yaml`:

```yaml
# Configuration/Services.yaml

services:
  Fr\Typo3HandlebarsComponents\Renderer\Component\Page\PageContentRendererInterface:
    alias: 'Vendor\Extension\Renderer\Component\Page\PageContentRenderer'
```
