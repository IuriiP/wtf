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

namespace Wtf\Traits;

/**
 * The adaptive Builder.
 * 
 * Descendants must defines the static methods `guess[_type0[_type1[_type2[...]]]]`
 * for building itself by the arguments types accordingly.
 * Types are defined by the function `gettype()`
 * 
 * EG:
 * guess_string
 * guess_array_string
 * etc.
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
trait AdaptiveFactory {

	/**
	 * Build the object of some class
	 * dependent of the arguments list
	 * 
	 * @return Object
	 */
	final static public function produce() {
		$args = func_get_args();
		$types = ['guess'];
		foreach($args as $arg) {
			$types[] = gettype($arg);
		}

		$self = new static();
		$ref = new \ReflectionClass($self);

		while($types) {
			$method = implode('_', $types);
			if($ref->hasMethod($method)) {
				return $self->$method(...$args);
			}
			array_pop($types);
		}
		return null;
	}

	/**
	 * Predefined basic guesser
	 * Return instance of the class
	 * 
	 * @return bool
	 */
	protected function guess() {
		return new static();
	}

}
