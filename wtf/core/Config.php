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
    Wtf\Core\Resource,
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

    /**
     * @var \Wtf\Core\Resource
     */
    private $_cfgRoot = null;

    private function __construct() {
        $this->_cfgRoot = App::get('path')->config;

        $list = $this->_cfgRoot->get();
        if ($this->_cfgRoot->isContainer()) {
            foreach ($list as $file) {
                $this->offsetSet($file->getName(), Resource::produce($this->_cfgRoot, $file));
            }
        } else {
            foreach ($list as $key => $line) {
                $this->offsetSet($key, $line);
            }
        }

        if ($bootstrap = $this['bootstrap']) {
            foreach ($bootstrap as $action) {
                if (is_callable([$action, 'bootstrap'])) {
                    call_user_func([$action, 'bootstrap']);
                }
            }
        }
    }

    /**
     * Override Container::offsetGet
     * 
     * @param string $offset
     * @return array
     */
    public function offsetGet($offset) {
        $cfg = $this->_container[strtolower($offset)];
        if ($cfg instanceof Resource) {
            // not loaded config
            $this->offsetSet($offset, $cfg = $this->load($cfg));
        }
        return $cfg;
    }

    /**
     * Internal config loading.
     * 
     * @param Resource $res
     * @return array
     */
    protected function load(Resource $res) {
        switch ($res->getType()) {
            case 'cfg':
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
            case 'engine':
                // 
                return $res;
        }
        return [];
    }

}
