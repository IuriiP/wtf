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
 * The Rule
 * 
 * Creator:
 * Rule::_($methods,$pattern,$callback)
 * 
 * Substitutions in patterns and string callbacks
 *  use: '{\w+}' (case sensitive!)
 *  if pattern name is UcFirst - result will be UcFirsted too!!!
 * 
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
class Rule implements \Wtf\Interfaces\Creator {

	use \Wtf\Traits\Creator;

	private static $_allowed = ['get', 'put', 'patch', 'post', 'delete'];

	private $_original = null;

	private $_methods = [];

	private $_pattern = null;

	private $_callback = null;

	private $_mapper = [];

	private $_group = [];

	private $_params = [];

	/**
	 * @param string|array $methods
	 * @param string $pattern
	 * @param Callable $callback may be string with substitutes ''
	 */
	function __construct($methods, $pattern, $callback = null) {
		if(!$methods || in_array('any', (array) $methods)) {
			$this->_methods = null;
		} elseif(!($this->_methods = array_intersect((array) $methods, self::$_allowed))) {
			throw new \Wtf\Exceptions\ArgumentsException('[' . join(',', (array) $methods) . ']');
		}
		$this->_mapper = ['_'];
		$this->_callback = $callback;
		$this->_original = $pattern;
		$this->_pattern = preg_replace_callback('#{((\w+)(:([^}\s]+))?)(.*?)}#', function($matches) {
			$name = $matches[2];
			$mask = $matches[4] ? : '\w+';
			$this->_mapper[] = $name;
			return "({$mask})";
		}, $pattern);
	}

	/**
	 * Build the prefixed group of rules.
	 * 
	 * Rule::group($pattern,$rules)
	 * 
	 * @param string|array $methods
	 * @param string $pattern
	 * @param Rule[] $rules
	 * @param Callable $callback Description
	 * @return Rule
	 */
	public static function group($methods, $pattern, $rules, $callback) {
		$self = self::_($methods, $pattern, $callback);
		$self->_group = (array) $rules;
		return $self;
	}

	/**
	 * The universal (any method) rule.
	 * Rule::any($pattern,$callback)
	 *
	 * Direct function because 'any' not exists in allowed methods list.
	 * 
	 * @param string $pattern
	 * @param mixed $callback
	 * @return Rule
	 */
	public static function any($pattern, $callback) {
		return self::_(null, $pattern, $callback);
	}

	/**
	 * Static shortcuts for creating:
	 * 
	 * The get (CRUD:Read) rule.
	 * Rule::get($pattern,$callback) 
	 * 
	 * The the put (CRUD:Update_if_Exists) rule.
	 * Rule::put($pattern,$callback) 
	 * 
	 * The patch (CRUD:Update_if_Exists_or_Create) rule.
	 * Rule::patch($pattern,$callback) 
	 * 
	 * The post (CRUD:Create) rule.
	 * Rule::post($pattern,$callback) 
	 * 
	 * The delete (CRUD:Delete) rule.
	 * Rule::delete($pattern,$callback) 
	 * 
	 * 
	 * @param string $name
	 * @param array $arguments
	 * @return Rule
	 */
	public static function __callStatic($name, $arguments) {
		return self::_([$name], ...$arguments);
	}

	/**
	 * Merge named parameters.
	 * 
	 * @param array $matches
	 * @return array
	 */
	private function _expandParams($matches) {
		return $this->_params = array_replace($this->_params, array_combine($this->_mapper, (array) $matches));
	}

	/**
	 * Find the first matched rule from array.
	 * 
	 * @param Rule[] $rules
	 * @param string $path
	 * @param string $method
	 * @param array $params
	 * @return Rule
	 */
	public static function find($rules, $path, $method, $params = []) {
		if($method && in_array($method, self::$_allowed)) {
			foreach($rules as $rule) {
				$ret = $rule->_try($path, $method, $params);
				if($ret) {
					return $ret;
				}
			}
		} else {
			throw new \Wtf\Exceptions\ArgumentsException(__METHOD__ . "(method='$method')");
		}

		return null;
	}

	/**
	 * Check pattern and return rule if matchs.
	 * 
	 * @param string $path
	 * @param string $method
	 * @param array $parsed
	 * @return Rule | null
	 */
	public function _try($path, $method, $params) {
		if(!$this->_methods || in_array($method, $this->_methods)) {
			$matches = [];
			if(preg_match("#^{$this->_pattern}(.*)$#", $path, $matches)) {
				$this->_params = $params;
				$matches[0] = count($matches) === count($this->_mapper) ? '' : array_pop($matches);
				$this->_expandParams($matches);
				if($this->_group) {
					$ret = self::find($this->_group, $matches[0], $method, $this->_params);
					if($ret || !$this->_callback) {
						return $ret;
					}
				}
				return $this;
			}
		}

		return null;
	}

	/**
	 * Call the specified method.
	 * Callable must be in form:
	 * function($method,$named_args);
	 * the $named_args[0] contains the tail from parsed source.
	 * 
	 * E.G.:
	 * on source '/application/action/path/name'
	 * with pattern '/{app}/{act}'
	 * $named_args = [
	 * 0 => '/path/name',
	 * 'app' => 'application',
	 * 'act' => 'action'
	 * ];
	 * 
	 * @param Request $request
	 * @return Responce
	 * @throws \ErrorException
	 */
	public function execute($request) {
		$callback = $this->_callback;
		$params = $this->_params;
		$format = $params['format'] = $request->format;
		$method = $params['method'] = $request->method;

		if(is_string($callback)) {
			$callback = explode('::', $this->_substitute($callback, $params));
		}

		if(is_array($callback)) {
			if(count($callback) < 2) {
				$callback[1] = $method;
			} else {
				$callback[1] = $this->_substitute($callback[1], $params);
			}

			$class = $callback[0];
			if(is_string($class)) {
				$contract = App::contract($class) ? : $class;
				if(is_string($contract)) {
					$ref = new \ReflectionClass($class);
					$callback[0] = $ref->newInstance(App::singleton());
				} else {
					$callback[0] = $contract;
				}
			}
		}

		if(is_callable($callback)) {
			return call_user_func($callback, $request, $this->params);
		}
		throw new \Wtf\Exceptions\ClosureException($this->_original);
	}

	private function _substitute($string, $params) {
		return preg_replace_callback('#{(\w+)}#', function($matches) use($params) {
			$match = $matches[1];
			if(!isset($params[$match])) {
				throw new \Wtf\Exceptions\ArgumentsException($this->_callback);
			}
			return ctype_upper($match{0}) ? ucfirst($params[$match]) : $params[$match];
		}, $string);
	}

}
