<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "handlebars_components".
 *
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

namespace Cpsit\Typo3HandlebarsComponents\DataProcessing;

use Fr\Typo3Handlebars\DataProcessing\AbstractDataProcessor;
use Cpsit\Typo3HandlebarsComponents\Data\PageProvider;

/**
 * PageProcessor
 *
 * @author Martin Adler <m.adler@familie-redlich.de>
 * @license GPL-2.0-or-later
 *
 * @property PageProvider $provider
 */
class PageProcessor extends AbstractDataProcessor
{
    protected function render(): string
    {
        $this->provider->setContentObjectRenderer($this->cObj);
        $data = $this->provider->get($this->cObj->data, $this->configuration);

        return $this->presenter->present($data);
    }
}
