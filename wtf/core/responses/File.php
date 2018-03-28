<?php

/*
 * Copyright (C) 2017 IuriiP <hardwork.mouse@gmail.com>
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

namespace Wtf\Core\Responses;

/**
 * Description of File
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class File extends \Wtf\Core\Response {

	/**
	 *
	 * @var \Wtf\Core\Resource
	 */
	private $_resource = null;

	public function __construct($res, $child = null) {
		$base = \Wtf\Core\Resource::produce($res);
		$this->_resource = $child ? $base->child($child) : $base;
	}

	public function __toString() {
		if($this->_resource->exists() && !$this->_resource->isContainer()) {
			$this->headers([
				'Content-Type' => $this->_resource->getMime(),
				'Content-Length' => $this->_resource->getLength()
			]);
			return $this->_resource->getContent();
		}
		return null;
	}

	public function clear() {
		$this->_resource = null;
		return $this;
	}

}
