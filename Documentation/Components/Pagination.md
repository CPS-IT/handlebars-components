# Pagination

## Requirements

* Include TypoScript at `EXT:handlebars_components/Configuration/TypoScript/Pagination`

## Description

This component enables the rendering of a pagination using Handlebars. A
[`NumberedPagination`][1] is provided for this purpose. It renders a
pagination in the following form:

```
[1] … [3] [4] [5] … [10]
```

The rendering is made up of several individual components:

### Pagination

The [`NumberedPagination`][1] corresponds to a representation of the
generated pagination. It is based on a concrete implementation of the
[`PaginatorInterface`][2] from TYPO3 core. It holds the information about
items to be paginated as well as parameters for building the pagination,
such as the number of elements per page or the current page number.

### Pagination factory

For simplified instantiation of a pagination, the extension provides a
[`PaginationFactory`][3]. This creates an instance of [`NumberedPagination`][1]
including the underlying paginator on the basis of the items to be
paginated and other parameters. Currently, the following paginators are
supported:

| Item type | Resulting paginator | Description |
| --------- | ------------------- | ----------- |
| [`QueryResultInterface`][4] | [`QueryResultPaginator`][5] | Extbase query result |
| [`SearchResultSet`][6] | [`ResultsPaginator`][7] | Solr search result |
| _`iterable`_ | [`ArrayPaginator`][8] | Default array paginator |

:bulb: *Tip:* By extending the [`PaginationFactory::buildPaginator()`][3]
method, you can add support for other paginators, if needed.

### Template variables

The extension already provides a simple variant for rendering pagination templates.
For this purpose, a [`PaginationVariablesResolver`][9] exists that resolves a
transmitted [`NumberedPagination`][1] object into associated template variables.
The resolver uses the following template structure by default:

```
items
└── .[]
    ├── label
    ├── link
    └── current
```

Explanation: The `items` array contains multiple objects for mapping individual
elements in the pagination. Each item represents either a page or a placeholder
(`…`). Placeholders are only given a label. Pages, on the other hand, additionally
receive the generated link and the indication whether it is the current page number.

### Pagination linkers

Since the linking of individual pages can look different depending on the context
and the paginator used, this is done using so-called pagination linkers. The
[`PaginationLinkerInterface`][10] is available for this purpose. The extension
already provides the following pagination linkers:

| Type | Description | Class name |
| ---- | ----------- | ---------- |
| Default linker | Appends a query parameter `page` to the current request URL. | [`DefaultPaginationLinker`][11] |
| Solr linker | Uses the [`SearchUriBuilder`][12] from EXT:solr to generate URLs. | [`SolrSearchResultPaginationLinker`][13] |

## Example

Instantiate the pagination in your data provider:

```php
# Classes/Data/MyBeautifulProvider.php

namespace Vendor\Extension\Data;

use Fr\Typo3Handlebars\Data\DataProviderInterface;
use Fr\Typo3Handlebars\Data\Response\ProviderResponseInterface;
use Fr\Typo3HandlebarsComponents\Pagination\PaginationFactory;
use Vendor\Extension\Data\Response\MyBeautifulProviderResponse;

final class MyBeautifulProvider implements DataProviderInterface
{
    private PaginationFactory $paginationFactory;

    public function __construct(PaginationFactory $paginationFactory)
    {
        $this->paginationFactory = $paginationFactory;
    }

    public function get(array $data): ProviderResponseInterface
    {
        $items = $this->fetchItems();
        $pagination = $this->paginationFactory->get($items);

        // ...

        return new MyBeautifulProviderResponse($pagination, /* ... */);
    }
}
```

In your presenter, create the template variables and render your final template:

```php
# Classes/Presenter/MyBeautifulPresenter.php

namespace Vendor\Extension\Presenter;

use Fr\Typo3Handlebars\Data\Response\ProviderResponseInterface;
use Fr\Typo3Handlebars\Exception\UnableToPresentException;
use Fr\Typo3Handlebars\Presenter\AbstractPresenter;
use Fr\Typo3HandlebarsComponents\Pagination\Linker\DefaultPaginationLinker;
use Fr\Typo3HandlebarsComponents\Presenter\VariablesResolver\PaginationVariablesResolver;
use Vendor\Extension\Data\Response\MyBeautifulProviderResponse;

final class MyBeautifulPresenter extends AbstractPresenter
{
    private DefaultPaginationLinker $paginationLinker;
    private PaginationVariablesResolver $paginationVariablesResolver;

    public function __construct(
        DefaultPaginationLinker $paginationLinker,
        PaginationVariablesResolver $paginationVariablesResolver
    ) {
        $this->paginationLinker = $paginationLinker;
        $this->paginationVariablesResolver = $paginationVariablesResolver;
    }

    public function present(ProviderResponseInterface $data): string
    {
        if (!($data instanceof MyBeautifulProviderResponse)) {
            throw new UnableToPresentException('Received unexpected response from provider.', 1647948612);
        }

        // Add default variables
        $renderData = [
            // ...
        ];

        // Resolve pagination variables
        $renderData['pagination'] = $this->paginationVariablesResolver->resolve(
            $data->getPagination(),
            $this->paginationLinker
        );

        return $this->renderer->render(/* ... */, $renderData);
    }
}
```

[1]: ../../Classes/Pagination/NumberedPagination.php
[2]: https://github.com/TYPO3/typo3/blob/main/typo3/sysext/core/Classes/Pagination/PaginatorInterface.php
[3]: ../../Classes/Pagination/PaginationFactory.php
[4]: https://github.com/TYPO3/typo3/blob/main/typo3/sysext/extbase/Classes/Persistence/QueryResultInterface.php
[5]: https://github.com/TYPO3/typo3/blob/main/typo3/sysext/extbase/Classes/Pagination/QueryResultPaginator.php
[6]: https://github.com/TYPO3-Solr/ext-solr/blob/11.5.0-rc-1/Classes/Domain/Search/ResultSet/SearchResultSet.php
[7]: https://github.com/TYPO3-Solr/ext-solr/blob/11.5.0-rc-1/Classes/Pagination/ResultsPaginator.php
[8]: https://github.com/TYPO3/typo3/blob/main/typo3/sysext/core/Classes/Pagination/ArrayPaginator.php
[9]: ../../Classes/Presenter/VariablesResolver/PaginationVariablesResolver.php
[10]: ../../Classes/Pagination/Linker/PaginationLinkerInterface.php
[11]: ../../Classes/Pagination/Linker/DefaultPaginationLinker.php
[12]: https://github.com/TYPO3-Solr/ext-solr/blob/11.5.0-rc-1/Classes/Domain/Search/Uri/SearchUriBuilder.php
[13]: ../../Classes/Pagination/Linker/SolrSearchResultPaginationLinker.php
