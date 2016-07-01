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
 * Use vlucas/dotenv to set environment
 */
$dotenv = new \Dotenv\Dotenv(dirname(__DIR__));
/**
 * Load environment
 */
//$dotenv->load();
/**
 * Or overload environment
 */
$dotenv->overload();

/**
 * Create shortcut to main application!
 */
class App extends \Wtf\Core\App {
	// short alias
}

/**
 * Run application
 */
App::run($start_time);
