<?php
/*
 * Copyright (C) 2016 Iurii Prudius <hardwork.mouse@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * This file provide the bootstraping logic 
 * as set of the any callables.
 */
return [
    // predifined system contracts to singletons
    App::contract('session', Wtf\Core\Session::singleton()),
    App::contract('logger', Wtf\Core\Logger::singleton()),
    App::contract('profiler', Wtf\Core\Profiler::singleton()),
    App::contract('trashbin', Wtf\Core\Trashbin::singleton()),
    App::contract('database', Wtf\Core\Database::singleton()),
    // self registered contracts to common builders
    'Wtf\Core\Request',
    'Wtf\Core\Response',
    'Wtf\Core\Rule',
    'Wtf\Core\Resource',
];
