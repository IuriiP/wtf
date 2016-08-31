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
class View {

	static private $_caches = [];

	/**
	 * @param string $source
	 */
	public function __construct($source) {
		if($cached = self::find($source)) {
			parent::__construct($this->resolve($cached, $vars));
		} else {
			parent::__construct(null);
		}
	}

	public static function exists($view) {
		return self::find($view);
	}

	public static function find($view) {
		if($origin = self::search($view)) {
			$cached = realpath(\Wtf\Core\App::path('cache') . DIRECTORY_SEPARATOR . sha1($view));
			if(!file_exists($cached) || (filemtime($cached) < filemtime($origin['path']))) {
				file_put_contents($cached, Compiler::compile($origin['compiler'], $origin['path']));
			}
			return $cached;
		}
		return null;
	}

	static protected function search($view) {
		foreach(\Wtf\Core\App::config('views') as $key => $value) {
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

	protected function resolve($fname, $vars) {
		ob_start();
		extract($vars);
		include $fname;
		return ob_get_clean();
	}

}
