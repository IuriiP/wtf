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

namespace Wtf\Core;

/**
 * Description of I18n
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class I18n implements \Wtf\Interfaces\Configurable {

	use \Wtf\Traits\Configurable;

	/**
	 * Translation resources.
	 *
	 * @var \Wtf\Core\Resource[] 
	 */
	private static $_resources = [];

	/**
	 * Default translator.
	 *
	 * @var \Wtf\Core\I18n 
	 */
	private static $_default = null;

	/**
	 * Translator fallback cascade.
	 * 
	 * @var array of Wtf\Core\I18n\Domain
	 */
	private $_fallback = [];

	/**
	 * Init current translation engine to new default.
	 * 
	 * @param int $lang
	 */
	static public function init($lang = []) {
		static::$_default = new static($lang);
	}

	/**
	 * Construct translation logic by the weight.
	 * 
	 * @param array $lang [ `language` => `weight`, ... ]
	 * @param bool|string $fallback
	 */
	public function __construct($lang = [], $fallback = true) {
		if(!is_array($lang)) {
			$lang = $lang ? [$lang => 1] : [];
		}

		arsort($lang);
		if($fallback && ($fallback = is_string($fallback) ? $fallback : $this->config('fallback')) && isset($lang[$fallback])) {
			$lang[$fallback] = 0;
		}

		// init resources
		if(!static::$_resources) {
			static::$_resources = $this->config('resources');
		}

		// biuld fallback cascade
		$this->_fallback = [];
		if(static::$_resources) {
			foreach($lang as $key => $value) {
				if(isset(static::$_resources[$key]) && ($resource = static::$_resources[$key]) && !in_array($resource, $arr, true)) {
					$this->_fallback[$key] = $resource;
				}
			}
		}
	}

	/**
	 * Allow access to default engine via static calls.
	 * 
	 * @param string $method
	 * @param array $arguments
	 * @return mixed
	 */
	static public function __callStatic($method, $arguments = []) {
		if(!static::$_default) {
			static::init();
		}
		if(method_exists(static::$_default, $method)) {
			return call_user_method_array($method, static::$_default, $params);
		}
		return null;
	}

	/**
	 * Find the key in the domain with fallback,
	 * choose parameter specific form 
	 * and translate it with parameters.
	 * 
	 * @param string $domain
	 * @param string $key
	 * @param array $params
	 * @param string $unknown
	 * @return string
	 */
	public function get($domain, $key, $params = [], $unknown = '') {
		if(!($pattern = $this->find($domain, $key))) {
			return $key . ($params ? ': (' . implode(', ', \Wtf\Helper\Complex::arr2attr($params)) . ')' : '');
		}

		reset($params);
		do {
			$pattern = is_array($pattern) ? $this->choose($pattern, current($params)) : $pattern;
			next($params);
		} while(is_array($pattern));

		return $this->substitute($pattern, $params, $unknown);
	}

	/**
	 * Find the key in the domain with fallback.
	 * 
	 * @param string $domain
	 * @param string $key
	 * @return string|array|null
	 */
	public function find($domain, $key) {
		foreach($this->_fallback as $language => $link) {
			// 
			if($link && ($resource = $link->child($domain))) {
				if($pattern = $resource->children($key)) {
					return $pattern;
				}
			}
		}
		return null;
	}

	/**
	 * Choose from patterns by parameters values.
	 * 
	 * @param array $patterns
	 * @param int $param
	 * @return string
	 */
	public function choose($patterns, $param = false) {
		$patts = (array) $patterns;
		if($param) {
			foreach($patts as $key => $value) {
				if(is_numeric($key)) {
					if($param === $key) {
						return $patts[$key];
					}
				} elseif($key{0} === '=') {
					if($param == substr($key, 1)) {
						return $patts[$key];
					}
				} elseif($key{0} === '<') {
					if($param < substr($key, 1)) {
						return $patts[$key];
					}
				} elseif($key{0} === '>') {
					if($param > substr($key, 1)) {
						return $patts[$key];
					}
				}
			}
		}
		return $patts[0];
	}

	/**
	 * Translate pattern 
	 * 
	 * @param string $pattern
	 * @param array $params
	 * @param string $unknown
	 * @return string
	 */
	public function substitute($pattern, $params = [], $unknown = '') {
		$list = [];
		$format = mb_ereg_replace_callback('\{\:([a-z0-9])\}', function($matches) use($list, $params, $unknown) {
			$key = $matches[1];
			if(isset($params[$key])) {
				$list[] = $params[$key];
				return '%s';
			} else {
				return $unknown;
			}
		}
			, $pattern);
		return vsprintf($format, $list);
	}

}
