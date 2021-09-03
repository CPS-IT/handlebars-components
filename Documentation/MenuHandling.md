# Component: Menu handling

## Description

The extension provides basic implementations for menus.
These can be used to map different menus. In addition, there
is a [`MenuFactoryInterface`](../Classes/Domain/Factory/Dto/MenuFactoryInterface.php),
with whose implementation different menus can be created
depending on the type.

## Example

```php
# Classes/Domain/Factory/Dto/MenuFactory.php

namespace Vendor\Extension\Domain\Factory\Dto;

use Fr\Typo3HandlebarsComponents\Domain\Factory\Dto\MenuFactoryInterface;
use Fr\Typo3HandlebarsComponents\Domain\Model\Dto\Menu;
use Fr\Typo3HandlebarsComponents\Exception\UnsupportedTypeException;

class MenuFactory implements MenuFactoryInterface
{
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
        // TODO: Implement "buildMetaMenu()" method.
    }

    private function buildMainMenu(): Menu
    {
        // TODO: Implement "buildMainMenu()" method.
    }
}
```
