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
 * Rules builder.
 * 
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
class Rule implements \Wtf\Interfaces\Bootstrap, \Wtf\Interfaces\AdaptiveFactory {

	use \Wtf\Traits\AdaptiveFactory;

	/**
	 * The smart rules builder
	 * 
	 * Using:
	 * Rule::make($methods,$pattern,$closure)
	 * Rule::make($pattern,$closure) 
	 * $methods will be 'any' if omitted
	 * 
	 * @return array|null
	 */

	/**
	 * Using string as closure 
	 * 
	 * @param string $method
	 * @param string $pattern
	 * @param string $callback
	 * @return array|null
	 */
	static protected function guess_string_string_string($method, $pattern, $callback) {
		$rule = self::_prepare($pattern);
		$rule['method'] = $method;
		$rule['closure'] = $callback;
		return $rule;
	}

	/**
	 * Wrapper for multiple methods
	 * 
	 * @param array $methods
	 * @param string $pattern
	 * @param string $callback
	 * @return array|null
	 */
	static protected function guess_array_string_string($methods, $pattern, $callback) {
		return self::guess_string_string_string(implode(',', $methods), $pattern, $callback);
	}

	/**
	 * Using lambda-function as closure 
	 * 
	 * @param string $method
	 * @param string $pattern
	 * @param \Closure $lambda
	 */
	static protected function guess_string_string_object($method, $pattern, $lambda) {
		$rule = self::_prepare($pattern);
		$rule['method'] = $method;
		$rule['closure'] = $lambda;
		return $rule;
	}

	/**
	 * Wrapper for multiple methods
	 * 
	 * @param array $methods
	 * @param string $pattern
	 * @param \Closure $lambda
	 * @return array|null
	 */
	static protected function guess_array_string_object($methods, $pattern, $lambda) {
		return self::guess_string_string_object(implode(',', $methods), $pattern, $lambda);
	}

	/**
	 * Using the sub-rules array
	 * 
	 * @param string $method
	 * @param string $pattern
	 * @param array $array
	 */
	static protected function guess_string_string_array($method, $pattern, $array) {
		$rule = self::_prepare($pattern);
		$rule['pattern'] .='(.*)';
		$rule['method'] = $method;
		$rule['closure'] = $array;
	}

	/**
	 * Wrapper for multiple methods
	 * 
	 * @param array $methods
	 * @param string $pattern
	 * @param array $array
	 * @return array|null
	 */
	static protected function guess_array_string_array($methods, $pattern, $array) {
		return self::guess_string_string_array(implode(',', $methods), $pattern, $array);
	}

	/**
	 * Parse pattern to regexp string & mapper array.
	 * 
	 * @param string $pattern
	 * @return array
	 */
	final static private function _prepare($pattern) {
		$mapper = [''];
		preg_replace_callback('#{(\w+)}#', function($varname) use(&$mapper) {
			$mapper[] = $varname;
			return '(\w+)';
		}, $pattern);

		$mapper[] = '_';
		return [
			'pattern' => $pattern,
			'mapper' => $mapper,
		];
	}

	/**
	 * Shortcut for build for the any method.
	 * 
	 * EG:
	 * Rule::get($pattern,$closure)
	 * instead of
	 * Rule::build('get',$pattern,$closure)
	 * 
	 * @param string $pattern
	 * @param mixed $closure
	 * @return array|null
	 */
	public static function any($pattern, $closure) {
		return self::produce('', $pattern, $closure);
	}

	/**
	 * Shortcut for build by the method name.
	 * 
	 * E.G.:
	 * Rule::get($pattern,$closure)
	 * instead of
	 * Rule::build('get',$pattern,$closure)
	 * 
	 * @param string $method
	 * @param array $args
	 * @return array|null
	 */
	public static function __callStatic($method, $args) {
		if(2 === count($args)) {
			return self::produce($method, $args[0], $args[1]);
		}
		return null;
	}

	public static function bootstrap(App $app) {
		$app::contract('rule', __CLASS__);
	}

}
