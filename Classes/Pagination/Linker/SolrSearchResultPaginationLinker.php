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

namespace Cpsit\Typo3HandlebarsComponents\Pagination\Linker;

use ApacheSolrForTypo3\Solr\Domain\Search\SearchRequest;
use ApacheSolrForTypo3\Solr\Domain\Search\Uri\SearchUriBuilder;
use TYPO3\CMS\Core\Pagination\PaginationInterface;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * SolrSearchPaginationLinker
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class SolrSearchResultPaginationLinker implements PaginationLinkerInterface
{
    private SearchUriBuilder $searchUriBuilder;
    private ?SearchRequest $searchRequest = null;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->searchUriBuilder = $objectManager->get(SearchUriBuilder::class);
    }

    public function buildUrlForPage(PaginationInterface $pagination, int $page): string
    {
        if ($this->searchRequest === null) {
            throw new \RuntimeException(
                'Unable to build pagination link without a valid solr search request instance.',
                1645175669
            );
        }

        return $this->searchUriBuilder->getResultPageUri($this->searchRequest, $page);
    }

    public function setSearchRequest(?SearchRequest $searchRequest): self
    {
        $this->searchRequest = $searchRequest;
        return $this;
    }
}
