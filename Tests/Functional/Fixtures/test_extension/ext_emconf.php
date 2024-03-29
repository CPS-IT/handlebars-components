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
/* @phpstan-ignore-next-line */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Handlebars Components Test Extension',
    'description' => 'Test extension for EXT:handlebars_components',
    'category' => 'misc',
    'version' => '0.1.0',
    'state' => 'alpha',
    'constraints' => [
        'depends' => [
            'typo3' => '',
        ],
    ],
];
