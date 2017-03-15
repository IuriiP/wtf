<?php

namespace Wtf\Core;

use \Wtf\Dataset\Engine,
	\Wtf\Dataset\Error,
	\Wtf\Dataset\Result;

/**
 * Common Data incapsulation & interface.
 * 
 * Using (over Pool):
 *  $db_default = Dataset::instance();
 *  $db_local = Dataset::instance('local');
 *  $db_log = Dataset::instance('log');
 *  $db_cloud = Dataset::instance('cloud');
 *
 * @author IuriiP
 */
class Dataset implements \Wtf\Interfaces\Pool, \Wtf\Interfaces\Configurable {

	use \Wtf\Traits\Configurable,
	 \Wtf\Traits\Observable,
	 \Wtf\Traits\Pool;

	/**
	 * Dataset engine
	 * @var \Wtf\Dataset\Engine
	 */
	protected $_engine = null;

	public $id = null;

	/**
	 * Called by (Pool) self::instance()
	 * 
	 * @param string $name
	 */
	final private function __construct($name) {
		self::configure('datasets');
		$config = $this->config($name);
		$engine = $config['engine'];

		if($engine) {
			$this->_engine = Engine::make(['', $engine], $config);
			EventManager::observe($config['observe']);
		}

		$this->id = $name;
	}

	/**
	 * Execute method of Engine.
	 * 
	 * @return \Wtf\Dataset\Result
	 */
	final public function execute() {
		$params = func_get_args();
		$method = array_shift($params);
		return $this->__call($method, $params);
	}

	final public function __call($name, $args) {
		if($this->_engine) {
			if(method_exists($this->_engine, $name)) {
				return call_user_func_array([$this->_engine, $name], $args);
			}
			return new Result(null, new Error("Method {$this->id}::{$name}: not defined", Error::ERROR));
		}
		return new Result(null, new Error("Dataset::{$this->id}: engine not available", Error::ERROR));
	}

}
