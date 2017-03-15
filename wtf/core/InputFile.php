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

namespace Wtf\Core;

/**
 * Description of InputFile
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class InputFile {

	private $_descriptor = [
		//$_FILES['userfile']['name'] Original file name
		'name' => null,
		//$_FILES['userfile']['type'] Mime-type
		'type' => null,
		//$_FILES['userfile']['size'] Size
		'size' => 0,
		//$_FILES['userfile']['error'] Error code
		'error' => 0,
		//$_FILES['userfile']['tmp_name'] Temp name
		'tmp_name' => null,
	];

	public function __construct($descriptor = []) {
		$this->_descriptor = array_replace($this->_descriptor, $descriptor);
	}

	/**
	 * Get content.
	 * 
	 * @return string
	 */
	public function __toString() {
		if((UPLOAD_ERR_OK === $this->_descriptor['error']) && !empty($this->_descriptor['tmp_name']) && is_file($this->_descriptor['tmp_name'])) {
			return file_get_contents($this->_descriptor['tmp_name']) ? : '';
		}
		return '';
	}

	/**
	 * Copy to destination.
	 * 
	 * @param string $path
	 * @param mixed $name string - new name, null - original name, else - generate unique name.
	 * @return string realpath to saved file
	 * @throws \ErrorException
	 */
	public function store($path, $name = null) {
		if(UPLOAD_ERR_OK === $this->_descriptor['error']) {
			switch(gettype($name)) {
				case 'string':
					break;
				case 'NULL':
					$name = $this->_descriptor['name'];
					break;
				default:
					$name = time() . '.' . rand() . '.' . $this->_descriptor['name'];
			}
			$dest = $path . DIRECTORY_SEPARATOR . $name;

			if(@copy($this->_descriptor['tmp_name'], $dest)) {
				return $dest;
			}
		}
		throw new \Wtf\Exceptions\ResourceException($this->_descriptor['name']);
	}

	public function __get($name) {
		if(isset($this->_descriptor[$name])) {
			return $this->_descriptor[$name];
		}
		return null;
	}
}
