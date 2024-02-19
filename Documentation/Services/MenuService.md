# [Menu service](../../Classes/Service/MenuService.php)

The [`MenuService`](../../Classes/Service/MenuService.php) can be used to
render arbitrary menus using TypoScript configuration. The `MenuProcessor`
and `LanguageMenuProcessor` from the TYPO3 core are used for this purpose.
Alternatively, a custom `DataProcessor` can be written if specific data
processing is required.

A [`MenuConfiguration`](../../Classes/Service/Configuration/MenuConfiguration.php)
object must be passed to the service, which contains the TypoScript
configuration. Simplified factory methods are already available for this
purpose (see examples below).

The resolved menus are objects of [`Menu`][1] that contain various
[`MenuItem`][2] objects linking to a specific URL using the provided DTO
model [`Link`][3]. In order to simplify the build process of different
menus, the extension provides a [`MenuFactoryInterface`][4], with whose
implementation different menus can be created depending on their type
(see example at the end).

## Examples

### Default menu

[Documentation](https://docs.typo3.org/m/typo3/reference-typoscript/main/en-us/ContentObjects/Fluidtemplate/DataProcessing/MenuProcessor.html)

#### Directory of pages

[TypoScript reference](https://docs.typo3.org/m/typo3/reference-typoscript/main/en-us/ContentObjects/Hmenu/Index.html#special-directory)

```php
use \Cpsit\Typo3HandlebarsComponents\Service\Configuration\MenuConfiguration;

$rootPageId = 17;
$levels = 3;

$menuConfiguration = MenuConfiguration::directory($rootPageId, $levels);
$menu = $menuService->buildMenu($menuConfiguration);
```

#### List of pages

[TypoScript reference](https://docs.typo3.org/m/typo3/reference-typoscript/main/en-us/ContentObjects/Hmenu/Index.html#special-list)

```php
use \Cpsit\Typo3HandlebarsComponents\Service\Configuration\MenuConfiguration;

$pageIds = [7, 17, 27];

$menuConfiguration = MenuConfiguration::list($pageIds);
$menu = $menuService->buildMenu($menuConfiguration);
```

#### Rootline menu

[TypoScript reference](https://docs.typo3.org/m/typo3/reference-typoscript/main/en-us/ContentObjects/Hmenu/Index.html#special-rootline)

```php
use \Cpsit\Typo3HandlebarsComponents\Service\Configuration\MenuConfiguration;

$begin = 1;
$end = -1;

$menuConfiguration = MenuConfiguration::rootline($begin, $end);
$menu = $menuService->buildMenu($menuConfiguration);
```

### Language menu

[Documentation](https://docs.typo3.org/m/typo3/reference-typoscript/main/en-us/ContentObjects/Fluidtemplate/DataProcessing/LanguageMenuProcessor.html)

```php
use \Cpsit\Typo3HandlebarsComponents\Service\Configuration\MenuConfiguration;

$languages = []; // resolves to 'auto' (include all languages)
// or
$languages = [0, 1]; // resolves to languages with IDs "0" and "1"

$menuConfiguration = MenuConfiguration::language($languages);
$menu = $menuService->buildMenu($menuConfiguration);
```

### Custom menu

If a custom implementation is to be used to build a menu, a corresponding
`DataProcessor` must be created for this purpose. It can either provide a
ready-made [`Menu`](../../Classes/Domain/Model/Dto/Menu.php) object or
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
use \Cpsit\Typo3HandlebarsComponents\Service\Configuration\MenuConfiguration;
use \Vendor\Extension\DataProcessing\Menu\CustomMenuProcessor;

$menuConfiguration = MenuConfiguration::custom(CustomMenuProcessor::class);
$menu = $menuService->buildMenu($menuConfiguration);
```

### Using a custom `MenuFactory`

To summarize the creation of individual menus, a `MenuFactory` can be
implemented. This can then generate corresponding menus depending on
the menu type using the `MenuService` as described above.

```php
# Classes/Domain/Factory/Dto/MenuFactory.php

namespace Vendor\Extension\Domain\Factory\Dto;

use Cpsit\Typo3HandlebarsComponents\Domain\Factory\Dto\MenuFactoryInterface;
use Cpsit\Typo3HandlebarsComponents\Domain\Model\Dto\Menu;
use Cpsit\Typo3HandlebarsComponents\Exception\UnsupportedTypeException;
use Cpsit\Typo3HandlebarsComponents\Service\Configuration\MenuConfiguration;
use Cpsit\Typo3HandlebarsComponents\Service\MenuService;

class MenuFactory implements MenuFactoryInterface
{
    /**
     * @var MenuService
     */
    private $menuService;

    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    public function get(string $type): Menu
    {
        switch ($type) {
            case 'metaMenu':
                return $this->buildMetaMenu();
            case 'mainMenu':
                return $this->buildMainMenu();
            default:
                throw UnsupportedTypeException::create($type);
        }
    }

    private function buildMetaMenu(): Menu
    {
        $rootPageId = 27;
        $configuration = MenuConfiguration::directory($rootPageId);

        return $this->menuService->buildMenu($configuration)->setType('metaMenu');
    }

    private function buildMainMenu(): Menu
    {
        $rootPageId = 1;
        $configuration = MenuConfiguration::directory($rootPageId, 99);

        return $this->menuService->buildMenu($configuration)->setType('mainMenu');
    }
}
```

[1]: ../../Classes/Domain/Model/Dto/Menu.php
[2]: ../../Classes/Domain/Model/Dto/MenuItem.php
[3]: ../../Classes/Domain/Model/Dto/Link.php
[4]: ../../Classes/Domain/Factory/Dto/MenuFactoryInterface.php
