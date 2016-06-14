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

namespace Wtf\Core\Resources;

use Wtf\Helper\Common;

/**
 * Hashed file (dictionary)
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Hash extends \Wtf\Core\Resource implements \Wtf\Interfaces\Container {

    use \Wtf\Traits\Container;

    /**
     * @var \Wtf\Core\Resource
     */
    private $_resource = null;

    public function __construct($path, $options = array()) {
        $this->_resource = \Wtf\Core\Resource::produce($path, $options);
        $this->_opt = $options;
    }

    /**
     * Check if already exists.
     * 
     * @return bool
     */
    public function exists() {
        if ($this->_resource) {
            return $this->_resource->exists();
        }
        return false;
    }

    public function isContainer() {
        return true;
    }

    protected static function _parseComplex() {
        return null;
    }

    public function child($name) {
        $this->get();
        return $this->offsetGet($name);
    }

    public function container() {
        return $this->_resource;
    }

    public function getPath() {
        return $this->_resource->getPath();
    }

    public function getLength() {
        return $this->_resource->getLength();
    }

    public function getName() {
        return $this->_resource->getName();
    }

    public function getTime($type = null) {
        return $this->_resource->getTime($type);
    }

    public function getScheme() {
        return 'hash://';
    }

    public function getMime() {
        return 'application/json';
    }

    public function getType() {
        return 'engine';
    }

    public function get() {
        if (!$this->_container) {
            switch ($this->_resource->getType()) {
                case 'php':
                    // eval PHP file
                    $this->_container = (array) Common::parsePhp($this->_resource->getContent());
                    break;
                case 'json':
                    // JSON object as array
                    $this->_container = json_decode($this->_resource->getContent(), true);
                    break;
                case 'engine':
                    // try
                    $this->_container = $this->_resource;
                    break;
            }
        }
        return $this->_container;
    }

    public function getContent() {
        return json_encode($this->get());
    }

}
