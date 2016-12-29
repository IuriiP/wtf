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

use Wtf\Core\App,
	Wtf\Core\Response;

/**
 * Description of Request
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
class Request implements \Wtf\Interfaces\Bootstrap {

	private $_route = null;

	private $_map = null;

	private $_method = null;

	private $_input = [];

	public function __construct($route, $map = []) {
		$this->_route = $route? : '/';
		$this->_map = $map? : App::config('routes');
	}

	/**
	 * Process routes from list
	 * 
	 * @param string $method
	 * @param array $input
	 */
	public function execute($method = 'get', $input = []) {
		$this->_method = $method;
		$this->_input = $_REQUEST;

		if(!in_array($method, ['get', 'post'])) {
			$exploded = explode('&', file_get_contents('php://input'));
			foreach($exploded as $pair) {
				$item = explode('=', $pair);
				if(count($item) == 2) {
					$this->_input[urldecode($item[0])] = urldecode($item[1]);
				}
			}
		}
		$this->_input = array_replace($this->_input, $input);

		if($this->_map) {
			$ret = Rule::find($this->_map, $this->_route, $this->_method, $this->_input);
			if($ret) {
				return $ret;
			}
			return Response::error(404, ['route' => $this->_route]);
		}
		return Response::error(500, ['route' => $this->_route]);
	}

	/**
	 * Get value from $this->_input
	 * 
	 * @param string $name
	 * @param mixed $def
	 */
	final public function input($name, $def = null) {
		return \Wtf\Helper\Complex::extract($this->_input, $name, $def);
	}

	/**
	 * Get value from $_FILES
	 * 
	 * @param string $name
	 * @param mixed $def
	 */
	final public static function file($name, $def = null) {
		return \Wtf\Helper\Complex::extract($_FILES, $name, $def);
	}

	/**
	 * Implementation of \Wtf\Interfaces\Bootstrap::bootstrap()
	 * 
	 * @param App $app
	 */
	public static function bootstrap(\Wtf\Core\App $app) {
		$app::contract('request', __CLASS__);
	}

}
