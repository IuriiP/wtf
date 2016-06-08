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
class Hash extends \Wtf\Core\Resource implements \Wtf\Interfaces\Readable, \Wtf\Interfaces\Hashed\Hashed {

    private $_json = [];

    public function __construct($path, $options = array()) {
// $data ignored!
        $this->_origin = realpath($path);
        $this->_opt = $options;
    }

    public function getPath() {
        return $this->_origin;
    }

    public function getScheme() {
        return 'hash://';
    }

    public function isContainer() {
        return false;
    }

    public function child($name) {
        return null;
    }

    public function container() {
        $cont = dirname($this->_origin);
        return new File($cont, []);
    }

    public function getLength() {
        
    }

    public function getMime() {
        return 'application/json';
    }

    public function getName() {
        return pathinfo($this->_origin, PATHINFO_FILENAME);
    }

    public function getTime($type = null) {
        switch (strtolower($type)) {
            case 'c':
                return filectime($this->_origin);
            case 'a':
                return fileatime($this->_origin);
        }
        return filemtime($this->_origin);
    }

    public function getType() {
        return 'json';
    }

    public function byHash($hash, $divider = null) {
        if($divider && is_string($hash)) {
            $tree = explode($delimiter, $hash);
        } else {
            $tree = (array) $hash;
        }
        
        $branch = $this->_json;
        foreach ($tree as $key) {
            if(!isset($branch[$key])) {
                return null;
            }
            $branch = $branch[$key];
        }
        return $branch;
    }

    public function get($keep = false) {
        if (!$this->_json) {
            switch (pathinfo($this->_origin, PATHINFO_EXTENSION)) {
                case 'php':
                    // PHP as array
                    $this->_json = (array) \Wtf\Core\includeFile($this->_origin);
                    break;
                case 'json':
                    // JSON object as array
                    $this->_json = json_decode(file_get_contents($this->_origin), true);
                    break;
            }
        }
        return $this->_json;
    }

    public function getContent() {
        return json_encode($this->get());
    }

}
