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

/**
 * Description of Cache
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Cache extends \Wtf\Core\Resource implements \Wtf\Interfaces\Writable, \Wtf\Interfaces\Configurable {
    
    use \Wtf\Traits\Configurable;
    
    static private $_cached = null;
    private $_hash = null;
    private $_resource = null;

    public function __construct($path, $options = array()) {
        $this->_hash = hash($this->config('hash'), $path . ' ' . serialize($options));
        $this->_resource = \Wtf\Core\Resource::produce($path,$options);
    }

    public function append($data) {
        
    }

    public function child($name) {
        
    }

    public function container() {
        
    }

    public function get($keep = false) {
        
    }

    public function getContent() {
        
    }

    public function getLength() {
        
    }

    public function getMime() {
        
    }

    public function getName() {
        
    }

    public function getPath() {
        
    }

    public function getScheme() {
        
    }

    public function getTime($type = null) {
        
    }

    public function getType() {
        
    }

    public function isContainer() {
        
    }

    public function put($data) {
        
    }

    public function remove() {
        
    }

}
