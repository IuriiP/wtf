<?php

/*
 * Copyright (C) 2016 IuriiP <hardwork.mouse@gmail.com>
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

use Wtf\Core\Event;

/**
 * Description of Observable
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
trait Observable {

	private $_observe = [];

	public function observe($list = null) {
		if(!$list) {
			$this->_observe = [];
		} elseif(is_array($list)) {
			foreach($list as $event => $observer) {
				$this->_observe[] = $event;
				Event::enable($event, $observer);
			}
		} elseif($observer instanceof \Wtf\Interfaces\Observer) {
			$this->_observe = ['.*' => $observer];
			Event::enable('.*', $observer);
		}
	}

}
