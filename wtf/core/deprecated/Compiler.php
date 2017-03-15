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

namespace Wtf\Core;

/**
 * Compiler is a factory to template compilers.
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
abstract class Compiler implements \Wtf\Interfaces\Factory, \Wtf\Interfaces\Configurable {

	use \Wtf\Traits\Factory,
	 \Wtf\Traits\Configurable;

	/**
	 * Compile source content.
	 * 
	 * @param string $content
	 * @return string Compiled content
	 */
	abstract function compile($content);
}
