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
class Rule implements \Wtf\Interfaces\Bootstrap, \Wtf\Interfaces\Creator {

	use \Wtf\Traits\Creator;

	private $_methods = [];

	private $_mapper = [];

	private $_pattern = null;

	private $_callback = null;

	private $_group = [];

	/**
	 * The rules factory
	 * 
	 * Using:
	 * Rule::_($methods,$pattern,$callback)
	 * 
	 * and shortcuts:
	 * Rule::any($pattern,$callback) 
	 * Rule::get($pattern,$callback) 
	 * Rule::put($pattern,$callback) 
	 * Rule::patch($pattern,$callback) 
	 * Rule::post($pattern,$callback) 
	 * Rule::delete($pattern,$callback) 
	 * 
	 * or prefixed groups:
	 * Rule::group($pattern,$rules)
	 */
	function __construct($methods, $pattern, $callback = null) {
		$this->_methods = (!$methods || in_array('any', (array) $methods)) ? ['get', 'put', 'patch', 'post', 'delete'] : (array) $methods;
		$this->_mapper = [''];
		$this->_callback = $callback;
		$this->_pattern = preg_replace_callback('#{(\w+)}#', function($matches) use($this) {
			$this->_mapper[] = $matches[1];
			return '(\w+)';
		}, $pattern);
	}

	/**
	 * Build the prefixed group of rules.
	 * 
	 * Rule::group($pattern,$rules)
	 * 
	 * @param string $pattern
	 * @param array $rules
	 * @return Rule
	 */
	public static function group($pattern, $rules) {
		$self = self::_('', $pattern, null);
		$self->_group = (array) $rules;
		return $self;
	}

	/**
	 * Build the universal (any method) rule.
	 * 
	 * Rule::any($pattern,$callback)
	 * 
	 * @param string $pattern
	 * @param mixed $callback
	 * @return Rule
	 */
	public static function any($pattern, $callback) {
		return self::_(['get', 'put', 'patch', 'post', 'delete'], $pattern, $callback);
	}

	/**
	 * Build the get (CRUD:Read) rule.
	 * 
	 * Rule::get($pattern,$callback)
	 * 
	 * @param string $pattern
	 * @param mixed $callback
	 * @return Rule
	 */
	public static function get($pattern, $callback) {
		return self::_('get', $pattern, $callback);
	}

	/**
	 * Build the put (CRUD:Update/replace) rule.
	 * 
	 * Rule::put($pattern,$callback)
	 * 
	 * @param string $pattern
	 * @param mixed $callback
	 * @return Rule
	 */
	public static function put($pattern, $callback) {
		return self::_('put', $pattern, $callback);
	}

	/**
	 * Build the patch (CRUD:Update/modify) rule.
	 * 
	 * Rule::patch($pattern,$callback)
	 * 
	 * @param string $pattern
	 * @param mixed $callback
	 * @return Rule
	 */
	public static function patch($pattern, $callback) {
		return self::_('patch', $pattern, $callback);
	}

	/**
	 * Build the post (CRUD:Create) rule.
	 * 
	 * Rule::post($pattern,$callback)
	 * 
	 * @param string $pattern
	 * @param mixed $callback
	 * @return Rule
	 */
	public static function post($pattern, $callback) {
		return self::_('post', $pattern, $callback);
	}

	/**
	 * Build the delete (CRUD:Delete) rule.
	 * 
	 * Rule::delete($pattern,$callback)
	 * 
	 * @param string $pattern
	 * @param mixed $callback
	 * @return Rule
	 */
	public static function delete($pattern, $callback) {
		return self::_('delete', $pattern, $callback);
	}

	/**
	 * Merge named parameters.
	 * 
	 * @param array $input
	 * @param array $matches
	 * @return array
	 */
	private function _params($input, $matches) {
		return array_replace((array) $input, array_combine($this->_mapper, (array) $matches));
	}

	/**
	 * Check pattern and execute the callback if matchs.
	 * 
	 * @param string $string
	 * @param string $method
	 * @param array $input
	 * @return mixed Object or null
	 */
	public function execute($string, $method = null, $input = []) {
		if(!$method || in_array($method, $this->_methods)) {
			$matches = [];
			if(preg_match("#^{$this->_pattern}(.*)$#", $string, $matches)) {
				$matches[0] = count($matches) === count($this->_mapper) ? '' : array_pop($matches);
				return $this->_group ? self::find($this->_group, $matches[0], $method, $this->_params($input, $matches)) : $this->_execute($method,$this->_params($input, $matches));
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
	 * @param type $method
	 * @param type $params
	 * @return type
	 * @throws \ErrorException
	 */
	private function _execute($method,$params) {
		$callback = $this->_callback;
		if(is_string($callback)) {
			$callback = explode('::', preg_replace_callback('#{(w+)}#', function($matches) use($params) {
				$match = $matches[1];
				if(!isset($params[$match])) {
					throw new \ErrorException("Unknown argument '{$match}' in the rule '{$this->_pattern}'.");
				}
				return $params[$match];
			}, $callback));
			
			if($contract = App::get($callback[0])) {
				$callback[0] = $contract;
			}
			if(count($callback)<2) {
				$callback[1] = $method;
			}
		}
		
		if(is_callable($callback)) {
			return call_user_func($callback, $method, $params);
		}

		throw new \ErrorException("Target is not callable in the rule '{$this->_pattern}'.");
	}

	/**
	 * Find and try execute the first matched rule from array.
	 * 
	 * @param array $group
	 * @param string $string
	 * @param string $method
	 * @param array $input
	 * @return mixed
	 */
	public static function find($group, $string, $method, $input) {
		foreach($group as $rule) {
			$ret = $rule->execute($string, $method, $input);
			if($ret) {
				return $ret;
			}
		}

		return null;
	}

	/**
	 * Wtf\Interfaces\Bootstrap::bootstrap
	 * @param \Wtf\Core\App $app
	 */
	public static function bootstrap(App $app) {
		$app::contract('rule', self::class);
	}

}
