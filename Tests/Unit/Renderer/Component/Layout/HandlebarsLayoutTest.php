<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "handlebars_components".
 *
 * Copyright (C) 2021 Elias Häußler <e.haeussler@familie-redlich.de>
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

namespace Fr\Typo3HandlebarsComponents\Tests\Unit\Renderer\Component\Layout;

use Fr\Typo3HandlebarsComponents\Renderer\Component\Layout\HandlebarsLayout;
use Fr\Typo3HandlebarsComponents\Renderer\Component\Layout\HandlebarsLayoutAction;
use Symfony\Component\Console\Output\BufferedOutput;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * HandlebarsLayoutTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class HandlebarsLayoutTest extends UnitTestCase
{
    protected BufferedOutput $output;
    protected HandlebarsLayout $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->output = new BufferedOutput();
        $this->subject = new HandlebarsLayout(function (): void {
            $this->output->write('Hello world!');
        });
    }

    /**
     * @test
     */
    public function parseCallsRegisteredParseFunction(): void
    {
        self::assertFalse($this->subject->isParsed());

        $this->subject->parse();

        self::assertSame('Hello world!', $this->output->fetch());
        self::assertTrue($this->subject->isParsed());
    }

    /**
     * @test
     */
    public function addActionAddsHandlebarsLayoutAction(): void
    {
        $action = new HandlebarsLayoutAction([], 'trim');

        $this->subject->addAction('foo', $action);

        self::assertSame([$action], $this->subject->getActions('foo'));
    }

    /**
     * @test
     */
    public function getActionsReturnsAllActions(): void
    {
        self::assertCount(0, $this->subject->getActions());

        $this->subject->addAction('foo', new HandlebarsLayoutAction([], 'trim'));

        self::assertCount(1, $this->subject->getActions());
    }

    /**
     * @test
     */
    public function getActionsReturnsActionsByGivenName(): void
    {
        self::assertCount(0, $this->subject->getActions('foo'));

        $this->subject->addAction('foo', new HandlebarsLayoutAction([], 'trim'));

        self::assertCount(1, $this->subject->getActions('foo'));
    }

    /**
     * @test
     */
    public function hasActionTestsWhetherActionOfGivenNameIsRegistered(): void
    {
        self::assertFalse($this->subject->hasAction('foo'));

        $this->subject->addAction('foo', new HandlebarsLayoutAction([], 'trim'));

        self::assertTrue($this->subject->hasAction('foo'));
    }

    /**
     * @test
     */
    public function isParsedTestsWhetherParseFunctionWasAlreadyCalled(): void
    {
        self::assertFalse($this->subject->isParsed());

        $this->subject->parse();

        self::assertTrue($this->subject->isParsed());
    }

    /**
     * @test
     */
    public function setParsedSetsParsingState(): void
    {
        self::assertFalse($this->subject->setParsed(false)->isParsed());
        self::assertTrue($this->subject->setParsed(true)->isParsed());
    }
}
