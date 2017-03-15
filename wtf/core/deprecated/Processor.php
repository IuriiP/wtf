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
 * Processor is content specific controller
 * for postprocessing entities.
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
abstract class Processor extends Entity {

	/**
	 * Prepare processor
	 * 
	 * @param mixed $param
	 */
	public function __construct($param = []) {
		parent::__construct($param, 'processor');
	}

	/**
	 * Process given object
	 * 
	 * @param \Wtf\Core\Entity $object Description
	 * @return \Wtf\Core\Entity processed
	 */
	final public function process(Entity $object) {
		$method = 'process_' . $object->type;
		if(method_exists($this, $method)) {
			$this->$method($object);
		}
		return $object;
	}

	/**
	 * Processor haven't representative data.
	 * 
	 * @return string
	 */
	final public function __toString() {
		return '';
	}

}
