# Service: Menu

The [`MenuService`](../Classes/Service/MenuService.php) can be used to
render arbitrary menus using TypoScript configuration. The `MenuProcessor`
and `LanguageMenuProcessor` from the TYPO3 core are used for this purpose.
Alternatively, a custom `DataProcessor` can be written if specific data
processing is required.

A [`MenuConfiguration`](../Classes/Service/Configuration/MenuConfiguration.php)
object must be passed to the service, which contains the TypoScript
configuration. Simplified factory methods are already available for this
purpose (see examples below).

## Examples

### Default menu

[Documentation](https://docs.typo3.org/m/typo3/reference-typoscript/master/en-us/ContentObjects/Fluidtemplate/DataProcessing/MenuProcessor.html)

#### Directory of pages

[TypoScript reference](https://docs.typo3.org/m/typo3/reference-typoscript/master/en-us/ContentObjects/Hmenu/Index.html#special-directory)

```php
use \Fr\Typo3HandlebarsComponents\Service\Configuration\MenuConfiguration;

$rootPageId = 17;
$levels = 3;

$menuConfiguration = MenuConfiguration::directory($rootPageId, $levels);
$menu = $menuService->buildMenu($menuConfiguration);
```

#### List of pages

[TypoScript reference](https://docs.typo3.org/m/typo3/reference-typoscript/master/en-us/ContentObjects/Hmenu/Index.html#special-list)

```php
use \Fr\Typo3HandlebarsComponents\Service\Configuration\MenuConfiguration;

$pageIds = [7, 17, 27];

$menuConfiguration = MenuConfiguration::list($pageIds);
$menu = $menuService->buildMenu($menuConfiguration);
```

#### Rootline menu

[TypoScript reference](https://docs.typo3.org/m/typo3/reference-typoscript/master/en-us/ContentObjects/Hmenu/Index.html#special-rootline)

```php
use \Fr\Typo3HandlebarsComponents\Service\Configuration\MenuConfiguration;

$begin = 1;
$end = -1;

$menuConfiguration = MenuConfiguration::rootline($begin, $end);
$menu = $menuService->buildMenu($menuConfiguration);
```

### Language menu

[Documentation](https://docs.typo3.org/m/typo3/reference-typoscript/master/en-us/ContentObjects/Fluidtemplate/DataProcessing/LanguageMenuProcessor.html)

```php
use \Fr\Typo3HandlebarsComponents\Service\Configuration\MenuConfiguration;

$languages = []; // resolves to 'auto' (include all languages)
// or
$languages = [0, 1]; // resolves to languages with IDs "0" and "1"

$menuConfiguration = MenuConfiguration::language($languages);
$menu = $menuService->buildMenu($menuConfiguration);
```

### Custom menu

If a custom implementation is to be used to build a menu, a corresponding
`DataProcessor` must be created for this purpose. It can either provide a
ready-made [`Menu`](../Classes/Domain/Model/Dto/Menu.php) object or
alternatively a data structure which corresponds to that of the
`MenuProcessor` from TYPO3 core.

```php
# Classes/DataProcessing/Menu/CustomMenuProcessor.php

namespace Vendor\Extension\DataProcessing\Menu;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

class CustomMenuProcessor implements DataProcessorInterface
{
    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData): array
    {
        $targetVariableName = $processorConfiguration['as'] ?: 'menu';

        // TODO: Implement menu processing

        $processedData[$targetVariableName] = $processedMenu;

        return $processedData;
    }
}
```

The custom `DataProcessor` is now referenced via the TypoScript
configuration to handle the menu processing:

```php
use \Fr\Typo3HandlebarsComponents\Service\Configuration\MenuConfiguration;
use \Vendor\Extension\DataProcessing\Menu\CustomMenuProcessor;

$menuConfiguration = MenuConfiguration::custom(CustomMenuProcessor::class);
$menu = $menuService->buildMenu($menuConfiguration);
```
