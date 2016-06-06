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
class Dataset implements \Wtf\Interfaces\Pool, \Wtf\Interfaces\Configurable, \Wtf\Interfaces\Observable {

    use \Wtf\Traits\Configurable,
        \Wtf\Traits\Observable,
        \Wtf\Traits\Pool;

    /**
     * Dataset engine
     * @var \Wtf\Dataset\Engine
     */
    protected $_engine = null;
    protected $_id = null;

    /**
     * Called by (Pool) self::instance()
     * 
     * @param string $name
     */
    final private function __construct($name) {
        $config = $this->configure('datasets', $name);
        if ($engine = $this->config('engine')) {
            $this->_engine = Engine::make(['', $engine], $config);
        }
        if ($observe = $this->config('observe')) {
            $this->observe($observe);
        }

        $this->_id = $name;
    }

    /**
     * Execute method of Engine.
     * 
     * @return \Wtf\Dataset\Result
     */
    final public function execute() {
        if ($this->_engine) {
            $params = func_get_args();
            $method = array_shift($params);
            return call_user_method_array($method, $this->_engine, $params);
        }
        return new Result(null, new Error("Engine '{$this->_id}' not available", Error::ERROR));
    }

}
