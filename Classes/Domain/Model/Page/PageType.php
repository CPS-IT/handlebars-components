<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "handlebars_components".
 *
 * Copyright (C) 2021 Elias Häußler <e.haeussler@familie-redlich.de>
 * Copyright (C) 2021 Martin Adler <m.adler@familie-redlich.de>
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

namespace Fr\Typo3HandlebarsComponents\Domain\Model\Page;

use Fr\Typo3HandlebarsComponents\DynamicVariableInvocationTrait;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;

/**
 * PageType
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @author Martin Adler <m.adler@familie-redlich.de>
 * @license GPL-2.0-or-later
 *
 * @method bool isStandard()
 * @method bool isLink()
 * @method bool isShortcut()
 * @method bool isBackendUserSection()
 * @method bool isMountpoint()
 * @method bool isSpacer()
 * @method bool isSysFolder()
 * @method bool isRecycler()
 */
class PageType
{
    use DynamicVariableInvocationTrait;

    public const STANDARD = PageRepository::DOKTYPE_DEFAULT;
    public const LINK = PageRepository::DOKTYPE_LINK;
    public const SHORTCUT = PageRepository::DOKTYPE_SHORTCUT;
    public const BACKEND_USER_SECTION = PageRepository::DOKTYPE_BE_USER_SECTION;
    public const MOUNTPOINT = PageRepository::DOKTYPE_MOUNTPOINT;
    public const SPACER = PageRepository::DOKTYPE_SPACER;
    public const SYS_FOLDER = PageRepository::DOKTYPE_SYSFOLDER;
    public const RECYCLER = PageRepository::DOKTYPE_RECYCLER;

    /**
     * @var int
     */
    protected $type;

    public function __construct(int $type)
    {
        $this->type = $type;
    }

    public function is(int $type): bool
    {
        return $this->type === $type;
    }

    public function __toString(): string
    {
        return (string)$this->type;
    }
}
