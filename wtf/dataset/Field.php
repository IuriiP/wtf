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

use \Wtf\Dataset\Expression;

/**
 * Description of Field
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Field {

    private $_table = null;
    private $_name = null;
    private $_alias = null;
    private $_expression = null;

    public function __construct($name, $table=null, $alias=null) {
        $this->_table = $table;

        $matches = [];
        // parse alias
        if (preg_match('~(.+)\\s+as\\s+(.+)~i', $name, $matches)) {
            $this->_alias = $alias || $matches[2];
            $name = $matches[1];
        }
        // parse function
        if (preg_match('~(\\S)\\((.*)\\)~', $name, $matches)) {
            $this->_operator = '@';
            $this->_name = $matches[1];
            $this->_parameters = array_map(function($val) use ($table) {
                if (preg_match('~^[a-z]~i', $val)) {
                    return new Field($val, $table);
                }
                return $val;
            }, preg_split('~\\s*,\\s*~', $matches[2]));
        } else {
            $this->_operator = '';
            $this->_name = $name;
        }
    }

    public function alias($alias) {
        $this->_alias = $alias;
        return $this;
    }

    public function value($value) {
        if (!$this->_operator) {
            $this->_operator = '=';
        }
        $this->_value = $value;
        return $this;
    }

    public function operator($operator) {
        $this->_operator = $operator;
        return $this;
    }

    public function __get($name) {
        switch ($name) {
            case 'operator':
                return $this->_operator;
            case 'value':
                return $this->_value;
            case 'name':
                return $this->_name;
            case 'table':
                return $this->_table;
            case 'alias':
                return $this->_alias? : $this->_name;
            case 'parameters':
                return $this->_parameters;
        }
        return '';
    }

}
