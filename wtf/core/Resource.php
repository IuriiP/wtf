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
abstract class Resource implements \Wtf\Interfaces\Resource, \Wtf\Interfaces\Bootstrap, \Wtf\Interfaces\AdaptiveFactory {

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
	 * Builder dependent static methods
	 * returns the Resource descentant instance
	 */

	/**
	 * Clone Resource descendant
	 * from Resource
	 * 
	 * @param array $args
	 */
	final static protected function guess_object(array $args) {
		$obj = $args[0];
		if($obj instanceof Resource) {
			return self::factory($obj, [$obj->getPath(), $obj->getOptions(), $obj->getData()]);
		}
		return null;
	}

	/**
	 * Create Resource descendant
	 * from the child of Resource
	 * 
	 * @param array $args
	 */
	final static protected function guess_object_string(array $args, array $opts = []) {
		$obj = $args[0];
		if($obj instanceof Resource) {
			$parts = [];
			if(preg_match('~^([^?]*)(\?(.*))?$~', $args[1], $parts)) {
				$child = $parts[1];
				$data = (count($parts) > 2) ? $parts[3] : '';
				if('' === $child) {
					return self::factory($obj, [$obj->getPath(), $obj->getOptions(), $data])->options($opts);
				} elseif($child = $obj->child($child)) {
					return $child->data($data)->options($opts);
				}
			}
		}
		return null;
	}

	/**
	 * Create Resource descendant
	 * from the child of Resource 
	 * with adding options
	 * 
	 * @param array $args
	 */
	final static protected function guess_object_array(array $args) {
		$obj = $args[0];
		if($obj instanceof Resource) {
			return self::factory($obj, [$obj->getPath(), $obj->getOptions(), ''])->options($args[1]);
		}
		return null;
	}

	/**
	 * Create Resource descendant
	 * from the child of Resource 
	 * with adding options
	 * 
	 * @param array $args
	 */
	final static protected function guess_object_string_array(array $args) {
		return self::guess_object_string_array($args, $args[2]);
	}

	/**
	 * Create Resource descendant 
	 * from init URL string
	 * 
	 * @param array $args
	 */
	final static protected function guess_string(array $args) {
		if($parsed = self::_parseURL($args[0])) {
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
	final static protected function guess_string_array(array $args) {
		if($obj = self::guess_string($args)) {
			return $obj->options($args[1]);
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
	 * Specific constructor
	 */
	abstract public function __construct($path, $options = []);

	/**
	 * Get data as array
	 * 
	 * @return array
	 */
	abstract public function get($keep = false);

	/**
	 * Get binary data
	 * 
	 * @return string
	 */
	abstract function getContent();

	/**
	 * Check if resource exists.
	 * 
	 * @return bool
	 */
	abstract function exists();

	public static function bootstrap() {
		App::contract('resource', __CLASS__);
	}

}
