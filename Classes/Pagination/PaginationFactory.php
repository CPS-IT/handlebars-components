<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "handlebars_components".
 *
 * Copyright (C) 2022 Elias Häußler <e.haeussler@familie-redlich.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace Cpsit\Typo3HandlebarsComponents\Pagination;

use ApacheSolrForTypo3\Solr\Domain\Search\ResultSet\SearchResultSet;
use ApacheSolrForTypo3\Solr\Pagination\ResultsPaginator;
use Cpsit\Typo3HandlebarsComponents\Configuration\Extension;
use Cpsit\Typo3HandlebarsComponents\ServerRequestTrait;
use TYPO3\CMS\Core\Pagination\ArrayPaginator;
use TYPO3\CMS\Core\Pagination\PaginatorInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * PaginationFactory
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class PaginationFactory
{
    use ServerRequestTrait;

    protected const DEFAULT_ITEMS_PER_PAGE = 10;
    protected const DEFAULT_MAXIMUM_NUMBER_OF_LINKS = 5;

    /**
     * @var list<class-string>
     */
    protected static $supportedResultSets = [
        SearchResultSet::class,
        QueryResultInterface::class,
    ];

    /**
     * @var array{itemsPerPage: int, maximumNumberOfLinks: int}
     */
    protected $typoScriptConfiguration;

    public function __construct(ConfigurationManagerInterface $configurationManager)
    {
        $typoScriptSettings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            Extension::NAME
        );

        $this->typoScriptConfiguration = $this->parseTypoScriptConfiguration($typoScriptSettings);
    }

    /**
     * @param iterable<mixed>|QueryResultInterface<AbstractEntity>|SearchResultSet $items
     */
    public function get(
        $items,
        int $itemsPerPage = null,
        int $maximumNumberOfLinks = null,
        int $currentPageNumber = null
    ): NumberedPagination {
        $itemsPerPage ??= $this->typoScriptConfiguration['itemsPerPage'];
        $maximumNumberOfLinks ??= $this->typoScriptConfiguration['maximumNumberOfLinks'];
        $currentPageNumber ??= $this->getCurrentPageNumberFromRequest();

        $paginator = $this->buildPaginator($items, $currentPageNumber, $itemsPerPage);

        return new NumberedPagination($paginator, $maximumNumberOfLinks);
    }

    /**
     * @param iterable<mixed>|QueryResultInterface<AbstractEntity>|SearchResultSet $items
     */
    protected function buildPaginator($items, int $currentPageNumber, int $itemsPerPage): PaginatorInterface
    {
        if ($items instanceof QueryResultInterface) {
            return new QueryResultPaginator($items, $currentPageNumber, $itemsPerPage);
        }

        if ($items instanceof SearchResultSet) {
            return new ResultsPaginator($items, $currentPageNumber, $itemsPerPage);
        }

        if (is_iterable($items)) {
            return $this->resolveIterablePaginator($items, $currentPageNumber, $itemsPerPage);
        }

        /* @phpstan-ignore-next-line */
        throw new \UnexpectedValueException(
            sprintf(
                'Pagination items must be iterable or one of "%s", "%s" given.',
                implode('", "', self::$supportedResultSets),
                get_debug_type($items)
            ),
            1645114178
        );
    }

    protected function getCurrentPageNumberFromRequest(): int
    {
        $queryParams = self::getServerRequest()->getQueryParams();

        if (is_numeric($queryParams['page'] ?? null)) {
            return (int)$queryParams['page'];
        }

        return 1;
    }

    /**
     * @param iterable<mixed> $items
     */
    protected function resolveIterablePaginator($items, int $currentPageNumber, int $itemsPerPage): ArrayPaginator
    {
        if ($items instanceof \Traversable) {
            $items = iterator_to_array($items);
        }

        return new ArrayPaginator((array)$items, $currentPageNumber, $itemsPerPage);
    }

    /**
     * @param array<string, mixed> $typoScriptConfiguration
     * @return array{itemsPerPage: int, maximumNumberOfLinks: int}
     */
    protected function parseTypoScriptConfiguration(array $typoScriptConfiguration): array
    {
        $itemsPerPage = $typoScriptConfiguration['pagination']['itemsPerPage'] ?? null;
        $maximumNumberOfLinks = $typoScriptConfiguration['pagination']['maximumNumberOfLinks'] ?? null;

        return [
            'itemsPerPage' => (int)$itemsPerPage ?: self::DEFAULT_ITEMS_PER_PAGE,
            'maximumNumberOfLinks' => (int)$maximumNumberOfLinks ?: self::DEFAULT_MAXIMUM_NUMBER_OF_LINKS,
        ];
    }
}
