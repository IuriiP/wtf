<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Wtf\Traits;

/**
 * Description of Configurable
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
trait Configurable {

    static protected $s_config = null;
    protected $_config = null;

    public function configure($name=null) {
        if (!self::$s_config) {
            self::$s_config = \Wtf\Core\App::config((new \ReflectionClass($this))->getShortName());
        }
        if(self::$s_config) {
            return $this->_config = $name? self::$s_config[$name] : null;
        }
        return null;
    }

    public function config($path = null) {
        if (!$this->_config) {
            $this->configure();
        }
        if ($this->_config) {
            return $path ? $this->_config[$path] : $this->_config;
        }
        return null;
    }

}
