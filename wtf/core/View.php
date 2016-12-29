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

use Wtf\Core\Compiler;

/**
 * View is wrapper for finding and compling templates.
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
class View implements \Wtf\Interfaces\Creator, \Wtf\Interfaces\Configurable {

	use \Wtf\Traits\Creator,
		\Wtf\Traits\Configurable;

	/**
	 *
	 * @var string
	 */
	private $_cached = null;

	/**
	 * @param string $view
	 */
	public function __construct($view) {
		$this->_cached = self::find($view);
	}

	/**
	 * Get content
	 * 
	 * @return string
	 */
	public function get() {
		return $this->_cached;
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
			return Cache::supply($origin['path'], [Compiler::factory($origin['compiler']),'compile']);
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
			if(file_exists($filename)) {
				return [
					'path' => $filename,
					'compiler' => $value,
				];
			}
		}
		return null;
	}

	/**
	 * Render the compiled view on context
	 * 
	 * @param array Named variables content
	 * @param object Object context
	 * @return string
	 */
	public function render() {
		return \Wtf\Helper\Common::includePhp($this->_cached, ...func_get_args());
	}

}
