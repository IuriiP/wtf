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

use Wtf\Core\Resource,
	Wtf\Helper\Common;

/**
 * General Config access Class
 * 
 * @interface Container
 * @interface Singleton
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
class Config implements \Wtf\Interfaces\Singleton, \Wtf\Interfaces\Container {

	use \Wtf\Traits\Singleton,
	 \Wtf\Traits\Container;

	private $_resource = null;

	/**
	 * Prepare config
	 * 
	 * @param Resource|string $cfg Dependency injection
	 */
	public function __construct($cfg = null) {
		if(!$cfg) {
			$cfg = Server::config();
		}
		$this->load($cfg);
	}

	/**
	 * Overload current config.
	 * 
	 * @param Resource|string $cfg
	 * @return $this
	 */
	public function load($cfg) {
		if($cfg) {
			$cfgRoot = Resource::produce($cfg);
			if($cfgRoot->isContainer()) {
				$this->set([]);
				foreach($cfgRoot->get() as $file) {
					$res = Resource::produce($cfgRoot, $file);
					if($res->isContainer() || (false !== array_search($res->getType(), ['php', 'ini', 'json', 'xml']))) {
						$this->offsetSet($res->getName(), new Config($res));
					}
				}
			} else {
				$this->_resource = $cfgRoot;
			}
		}
		return $this;
	}

	/**
	 * Override Container::offsetGet
	 * 
	 * @param string $offset
	 * @return array
	 */
	public function offsetGet($offset) {
		$offset = strtolower($offset);
		if($this->_resource) {
			$this->set(self::_load($this->_resource));
			$this->_resource = null;
		}
		if(isset($this->_container[$offset])) {
			return $this->_container[$offset];
		}
		return null;
	}

	/**
	 * Internal config loading.
	 * 
	 * @param Resource $res
	 * @return array
	 */
	static protected function _load(Resource $res) {
		switch($res->getType()) {
			case 'php':
				// eval PHP file
				return Common::parsePhp($res->getContent());
			case 'json':
				// JSON object as array
				return json_decode($res->getContent(), true);
			case 'ini':
				// INI array
				return parse_ini_string($res->getContent(), true);
			case 'xml':
				// XML as array
				return json_decode(json_encode(simplexml_load_string($res->getContent())), true);
		}
		return [];
	}

}
