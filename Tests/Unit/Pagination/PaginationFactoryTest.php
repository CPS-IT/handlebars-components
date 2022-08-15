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

namespace Fr\Typo3HandlebarsComponents\Tests\Unit\Pagination;

use ApacheSolrForTypo3\Solr\Domain\Search\ResultSet\SearchResultSet;
use ApacheSolrForTypo3\Solr\Pagination\ResultsPaginator;
use Fr\Typo3HandlebarsComponents\Pagination\PaginationFactory;
use Fr\Typo3HandlebarsComponents\Tests\Unit\Fixtures\DummyConfigurationManager;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Pagination\ArrayPaginator;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * PaginationFactoryTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class PaginationFactoryTest extends UnitTestCase
{
    use ProphecyTrait;

    /**
     * @var DummyConfigurationManager
     */
    protected $configurationManager;

    /**
     * @var PaginationFactory
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configurationManager = new DummyConfigurationManager();
        $this->subject = new PaginationFactory($this->configurationManager);

        $this->simulateServerRequestWithoutPage();
    }

    /**
     * @test
     */
    public function getFetchesCurrentPageNumberFromRequest(): void
    {
        $this->simulateServerRequestWithPage(2);

        self::assertSame(2, $this->subject->get(['foo', 'baz'], 1)->getCurrentPageNumber());
    }

    /**
     * @test
     */
    public function getUsesDefaultCurrentPageNumber(): void
    {
        self::assertSame(1, $this->subject->get(['foo', 'baz'], 1)->getCurrentPageNumber());
    }

    /**
     * @test
     */
    public function getReturnsPaginationForQueryResult(): void
    {
        $queryProphecy = $this->prophesize(QueryInterface::class);
        $queryResultProphecy = $this->prophesize(QueryResultInterface::class);
        $queryResultProphecy->getQuery()->willReturn($queryProphecy->reveal());
        $queryResultProphecy->count()->willReturn(1);
        $queryProphecy->setLimit(Argument::type('int'))->willReturn($queryProphecy);
        $queryProphecy->setOffset(Argument::type('int'))->willReturn($queryProphecy);
        $queryProphecy->execute()->willReturn($queryResultProphecy->reveal());

        self::assertInstanceOf(
            QueryResultPaginator::class,
            $this->subject->get($queryResultProphecy->reveal())->getPaginator()
        );
    }

    /**
     * @test
     */
    public function getReturnsPaginationForSolrSearchResultSet(): void
    {
        self::assertInstanceOf(
            ResultsPaginator::class,
            $this->subject->get(new SearchResultSet())->getPaginator()
        );
    }

    /**
     * @test
     * @dataProvider getReturnsPaginationForIterableItemsDataProvider
     *
     * @param iterable<mixed> $items
     * @param array<mixed> $expected
     */
    public function getReturnsPaginationForIterableItems($items, array $expected): void
    {
        $actual = $this->subject->get($items);

        self::assertInstanceOf(ArrayPaginator::class, $actual->getPaginator());
        self::assertSame($expected, $actual->getPaginator()->getPaginatedItems());
    }

    /**
     * @test
     */
    public function getThrowsExceptionIfInvalidItemsAreGiven(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionCode(1645114178);

        /* @phpstan-ignore-next-line */
        $this->subject->get('foo');
    }

    /**
     * @return \Generator<string, array{iterable<mixed>, array<mixed>}>
     */
    public function getReturnsPaginationForIterableItemsDataProvider(): \Generator
    {
        $array = ['foo', 'baz'];
        $generator = function () use ($array): \Generator {
            yield from $array;
        };

        yield 'array' => [$array, $array];
        yield 'Generator' => [$generator(), $array];
        yield 'ArrayObject' => [new \ArrayObject($array), $array];
    }

    private function simulateServerRequestWithPage(?int $pageNumber): void
    {
        $serverRequest = new ServerRequest('https://www.example.com');

        if ($pageNumber !== null) {
            $serverRequest = $serverRequest->withQueryParams(['page' => $pageNumber]);
        }

        $GLOBALS['TYPO3_REQUEST'] = $serverRequest;
    }

    private function simulateServerRequestWithoutPage(): void
    {
        $this->simulateServerRequestWithPage(null);
    }
}
