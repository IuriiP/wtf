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

use Field,
    Expression,
    Join,
    Where;

/**
 * Description of Query
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Query {

    private $_table = null;
    private $_alias = null;
    private $_action = null;
    private $_distinct = false;
    private $_fields = [];
    private $_joins = [];
    private $_wheres = [];
    private $_havings = [];
    private $_group = [];
    private $_order = [];
    private $_offset = 0;
    private $_limit = 0;

    public function __construct($table, $alias = null) {
        $this->_table = $table;
        $this->_alias = $alias ? : $table;
    }

    public function join($table, Join $join) {
        $this->_joins[$join->alias()] = $join($table);
        return $this;
    }

    public function where(Where $where, $glue = 'AND') {
        $this->_wheres[] = $where($glue);
        return $this;
    }

    public function having(Where $where, $glue = 'AND') {
        $this->_havings[] = $where($glue);
        return $this;
    }

    public function group($alias) {
        $this->_group[$alias] = $alias;
        return $this;
    }

    public function ascending($alias) {
        $this->_order[$alias] = false;
        return $this;
    }

    public function descending($alias) {
        $this->_order[$alias] = true;
        return $this;
    }

    public function offset($offset) {
        $this->_offset = $offset;
        return $this;
    }

    public function limit($limit) {
        $this->_limit = $limit;
        return $this;
    }

    public function fields($fields = []) {
        $ret = $this->_fields;
        $this->_fields = $fields;
        return $ret;
    }

    public function field($name, $alias = null, Expression $expr = null) {
        $field = new Field($name, $this->_alias);
        $value = $field->expression($expr);
        $alias = $field->alias($alias);
        $this->_fields[$alias] = $value;
        return $this;
    }
    
}
