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
 * Description of View
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class View extends \Wtf\Core\Response implements \Wtf\Interfaces\Configurable {

	use \Wtf\Traits\Configurable;
	
	protected $_cached = null;
	protected $_data = [];

	public function __construct($name, $arguments) {
		$this->_cached = self::find($name);
		$this->_data = $arguments;
	}

	public function __toString() {
		if($this->_cached) {
			$text = \Wtf\Helper\Common::includePhp($this->_cached, $this->_data);
			$this
				->header('Content-Type', 'text/html')
				->header('Content-Length', strlen($text));
			return $text;
		}
		return null;
	}

	public function clear() {
		$this->_cached = null;
		$this->_data = [];
		return $this;
	}

	/**
	 * Find cached or compile & caching view
	 * 
	 * @param string $view
	 * @return string
	 */
	protected static function find($view) {
		$origin = self::search($view);
		if($origin) {
			return \Wtf\Core\Cache::supply($origin['path'], [\Wtf\Core\Compiler::factory($origin['compiler']),'compile']);
		}
		return null;
	}

	/**
	 * Search file by configured pathes
	 * 
	 * @param string $view
	 * @return array
	 */
	protected static function search($view) {
		foreach(self::configure() as $key => $value) {
			$filename = sprintf($key, $view);
			if(is_file($filename)) {
				return [
					'path' => $filename,
					'compiler' => $value,
				];
			}
		}
		return null;
	}

}
