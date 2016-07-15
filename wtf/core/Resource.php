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
 * Virtual Universal Resource Factory
 * 
 * Use URI notation:
 * [`protocol`://][`access`@][`path`][?`data`]
 * 
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
class Resource implements \Wtf\Interfaces\Bootstrap, \Wtf\Interfaces\AdaptiveFactory, \Wtf\Interfaces\Factory {

	use \Wtf\Traits\AdaptiveFactory,
	 \Wtf\Traits\Factory;

	/**
	 * Object specific options
	 * 
	 * @var array 
	 */
	protected $_opt = [];

	/**
	 * Object specific data
	 * 
	 * @var mixed
	 */
	protected $_data = null;

	/**
	 * Clone Resource descendant
	 * from Resource
	 * 
	 * @param Resource $obj
	 */
	final protected function guess_object(Resource $obj) {
		return $this->guess_object_string_array($obj, '', []);
	}

	/**
	 * Create Resource descendant
	 * from the child of Resource
	 * 
	 * @param Resource $obj
	 * @param string $string
	 */
	final protected function guess_object_string(Resource $obj, $string) {
		return $this->guess_object_string_array($obj, $string, []);
	}

	/**
	 * Create Resource descendant
	 * from the child of Resource 
	 * with adding options
	 * 
	 * @param Resource $obj
	 * @param array $opts
	 */
	final protected function guess_object_array(Resource $obj, array $opts) {
		return $this->guess_object_string_array($obj, '', $opts);
	}

	/**
	 * Create Resource descendant
	 * from the child of Resource 
	 * with adding options
	 * 
	 * @param Resource $obj
	 * @param string $string
	 * @param array $opts
	 */
	final protected function guess_object_string_array(Resource $obj, $string, array $opts) {
		$parts = [];
		if(preg_match('~^([^?]*)(\?(.*))?$~', $string, $parts)) {
			$branch = $parts[1];
			$data = (count($parts) > 2) ? $parts[3] : '';
			if(!strlen($branch)) {
				return static::factory($obj, [$obj->getPath(), $obj->getOptions(), $data])->options($opts);
			} elseif($child = $obj->child($branch)) {
				return $child->data($data)->options($opts);
			}
		}
		return null;
	}

	/**
	 * Create Resource descendant 
	 * from init URL string
	 * 
	 * @param string $string
	 */
	final protected function guess_string($string) {
		$parsed = self::_parseURL($string);
		if($parsed) {
			return static::factory($parsed['scheme'], [$parsed['path'], $parsed['options'], $parsed['data']]);
		}
		return null;
	}

	/**
	 * Create Resource descendant 
	 * from init URL string
	 * with options array
	 * 
	 * @param array $args
	 */
	final protected function guess_string_array($string, array $opts) {
		if($obj = $this->guess_string($string)) {
			return $obj->options($opts);
		}
		return null;
	}

	/**
	 * Create the object from URL
	 * 
	 * @param string $url
	 * @return array
	 */
	final static private function _parseURL($url) {
		$parts = [];
		if(preg_match('~^(([\\w]+)://)?((.+)@)?([/\\\\]?([^?]*))(\\?(.*))?$~i', $url, $parts)) {
			/* @var array */
			$opt = [];
			$access = explode(':', $parts[4], 2);
			if(!empty($access[0])) {
				$opt['user'] = $access[0];
			}
			if(!empty($access[1])) {
				$opt['password'] = $access[1];
			}

			return [
				'scheme' => ['', $parts[2] ? : 'file'],
				'path' => $parts[6]? : '/',
				'options' => $opt,
				'data' => count($parts) > 7 ? $parts[8] : ''
			];
		}

		return [
			'scheme' => ['', 'none'],
			'path' => '',
			'options' => [],
			'data' => ''
		];
	}

	/**
	 * Alias for isContainer
	 * 
	 * @return boolean
	 */
	final public function hasChildren() {
		return $this->isContainer();
	}

	/**
	 * Alias for child
	 * 
	 * @param string $name
	 * @return \Wtf\Core\Resource descendant
	 */
	final public function getChild($name) {
		return $this->child($name);
	}

	/**
	 * Alias for container
	 * 
	 * @return \Wtf\Core\Resource
	 */
	final public function getContainer() {
		return $this->container();
	}

	/**
	 * Get options
	 * 
	 * @return array
	 */
	final public function getOptions() {
		return $this->_opt;
	}

	/**
	 * Set options
	 * 
	 * @param array $opts
	 * @return array
	 */
	final public function setOptions($opts = []) {
		return $this->_opt = $opts;
	}

	/**
	 * Expand options
	 * 
	 * @param array $opts
	 * @return array
	 */
	final public function addOptions($opts = []) {
		return $this->_opt = array_replace_recursive($this->_opt, $opts);
	}

	/**
	 * Chainable add options
	 * 
	 * @param type $opts
	 * @return \Wtf\Core\Resource
	 */
	final public function options($opts = []) {
		$this->addOptions($opts);
		return $this;
	}

	/**
	 * Get data
	 * 
	 * @return string
	 */
	final public function getData() {
		return $this->_data;
	}

	/**
	 * Set data
	 * 
	 * @param string $data
	 * @return string
	 */
	final public function setData($data = '') {
		return $this->_data = $data;
	}

	/**
	 * Chainable set data
	 * 
	 * @param type $data
	 * @return \Wtf\Core\Resource
	 */
	final public function data($data = '') {
		$this->_data = $data;
		return $this;
	}

	/**
	 * Pseudo constructor
	 */
	private function __construct() {
		
	}

	static public function bootstrap(App $app) {
		$app::contract('resource', __CLASS__);
	}

}
