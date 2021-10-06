<?php

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

/** @noinspection PhpUndefinedVariableInspection */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Handlebars Components',
    'description' => 'Additional components for EXT:handlebars',
    'category' => 'fe',
    'version' => '0.3.0',
    'state' => 'alpha',
    'clearCacheOnLoad' => true,
    'author' => 'Elias Häußler',
    'author_email' => 'e.haeussler@familie-redlich.de',
    'author_company' => 'familie redlich digital GmbH',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-11.5.99',
            'handlebars' => '0.7.0-0.7.99',
        ],
    ],
];
