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
$start_time = microtime(true);
session_start();
/**
 * Use Composer's autoloader
 * 
 * @var \Composer\Autoload\ClassLoader Autoloader
 */
$loader = require_once('../vendor/autoload.php');

/**
 * Use iuriip/dotini to set initial constants
 */
$dotini = new \Dotini\Dotini(true);
/**
 * Initialize from '../'
 */
$dotini->load(dirname(__DIR__));

/**
 * Create shortcut to main application
 * to avoid using the string
 * use Wtf\Core\App;
 */
class App extends \Wtf\Core\App {
	// shortcut
}

/**
 * Run application
 */
App::run($start_time);
