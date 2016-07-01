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

namespace Wtf\Dataset;

use \Wtf\Dataset\Query,
	\Wtf\Dataset\Result;

/**
 * Description of Engine
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
abstract class Engine implements \Wtf\Interfaces\Factory, \Wtf\Interfaces\Crud {

	use \Wtf\Traits\Factory;

	private $_config = null;

	private $_request = null;

	private $_error = null;

	private $_result = null;

	/**
	 * Construct with config
	 * 
	 * @param array $config
	 */
	final public function __construct($config) {
		$this->_config = $config;
	}

	/**
	 * Get config subset
	 * 
	 * @return array
	 */
	final public function config($only = []) {
		return $only ? array_intersect_key($this->_config, array_fill_keys($only, true)) : $this->_config;
	}

	/**
	 * Map config options to driver-specific constants.
	 * I.e. in config file:
	 * option1=100
	 * option2=some text
	 * option3=20
	 * 
	 * Direct remap:
	 * Engine->options([OPTION1=>'option1',OPTION2=>'option2',OPTION4=>'option4']) 
	 * will produce:
	 * [OPTION1=>100,OPTION2=>'some text'] // option3 is omitted in array, option4 is omitted in config
	 * 
	 * Remap with default:
	 * Engine->options([OPTION1=>['option1'=>5],OPTION4=>['option4'=>333]])
	 * will produce:
	 * [OPTION1=>100,OPTION4=>333] // option4 is omitted in config and default value used
	 * 
	 * Remap by list:
	 * Engine->options([OPTION1=>['option1'=>[10=>'ten',100=>'hundred']],OPTION3=>['option3'=>[3=>'three']],OPTION4=>['option4'=>[''=>'default',2=>'two']]]) 
	 * will produce:
	 * [OPTION1=>'hundred',OPTION4=>'default'] // option3 value not in list and has no default, option4 is omitted in config but has default
	 * 
	 * Remap by list multiply:
	 * Engine->options([OPTION1=>['option1','option3']]) 
	 * will produce:
	 * [OPTION1=>'hundred',OPTION4=>'default'] // option3 value not in list and has no default, option4 is omitted in config but has default
	 * 
	 * 
	 * @param array $array Contains named driver-specific options
	 * @return array
	 */
	final public function options($array = []) {
		$maped = [];
		foreach($array as $key => $val) {
			if(is_array($val)) {
				// complex remap
				$remap = [];
				foreach($val as $subkey => $subval) {
					if(is_scalar($subval)) {
						// use config or default
						$remap[] = isset($this->_config[$subkey]) ? $this->_config[$subkey] : $subval;
					} elseif(isset($this->_config[$subkey]) && isset($subval[$this->_config[$subkey]])) {
						// conventional remap
						$remap[] = $subval[$this->_config[$subkey]];
					} elseif(isset($subval[''])) {
						// default remap
						$remap[] = $subval[''];
					}
				}
				$maped[$key] = array_reduce($remap, function($carry, $item) {
					if($carry) {
						if(is_numeric($carry) && is_numeric($item)) {
							return $carry + $item;
						}
						return $carry . $item;
					}
					return $item;
				});
			} elseif(isset($this->_config[$val])) {
				$maped[$key] = $this->_config[$val];
			}
		}
		return $maped;
	}

	/**
	 * Check ready state
	 * 
	 * @return bool
	 */
	abstract public function isReady();

	/**
	 * Real opening connection
	 * 
	 * @return bool
	 */
	abstract public function open();

	/**
	 * Real closing connection
	 * 
	 * @return bool
	 */
	abstract public function close();

	/**
	 * Get engine last error
	 * 
	 * @return \Wtf\Dataset\Error
	 */
	abstract public function error();

	/**
	 * Conditional getter
	 * 
	 * @param \Wtf\Dataset\Query $query
	 * @param boolean $need Create new if not exists
	 * @return \Wtf\Dataset\Result Set
	 */
	public function get(Query $query, $need = false) {
		$exists = $this->read($query->reading(), $query->conditions());
		if(!$exists->count && $need) {
			$this->create($query->creating());
			$exists = $this->read($query->reading(), $query->conditions());
		}
		return $exists;
	}

	/**
	 * Conditional setter
	 * 
	 * @param \Wtf\Dataset\Query $query
	 * @param boolean $existed Only update existed
	 * @return \Wtf\Dataset\Result Set
	 */
	public function set(Query $query, $existed = false) {
		$exists = $this->read($query->reading(), $query->conditions());
		if($exists->count) {
			$this->update($query->updating(), $query->conditions());
			$exists = $this->read($query->reading(), $query->conditions());
		} elseif(!$existed) {
			$this->create($query->creating());
			$exists = $this->read($query->reading(), $query->conditions());
		}
		return $exists;
	}

	/**
	 * Default for overloading
	 * 
	 * @param type $code
	 * @return type
	 */
	static public function getMessage($code) {
		$engine = static::class;
		return "Engine::{$engine}: error #{$code}";
	}

}
