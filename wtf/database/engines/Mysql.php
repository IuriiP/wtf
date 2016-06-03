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

namespace Wtf\Database\Engines;

use Wtf\Helper\Complex,
    Wtf\Database\Query,
    Wtf\Database\Field,
    Wtf\Database\Error,
    Wtf\Database\Result;

/**
 * Mysql engine
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Mysql extends \Wtf\Database\Engine implements \Wtf\Interfaces\Sql {

    private $_pdo = null;

    /**
     * @var \Wtf\Database\Error 
     */
    private $_error = null;

    /**
     * Check connection to database.
     * Connect if not connected.
     * Reset internal error marker or raise connection error.
     * 
     * @return \PDO | null
     */
    public function open() {
        $this->_error = null;

        if (!$this->_pdo) {
            $config = $this->config();
            $dsn = Complex::arr2attr($config, [
                        'host', 'port', 'dbname', 'charset',
            ]);
            try {
                $this->_pdo = new \PDO('mysql:' . implode(';', $dsn), Complex::get($config, 'user'), Complex::get($config, 'password'), $this->options(['persistent' => \PDO::ATTR_PERSISTENT]));
            } catch (\PDOException $exc) {
                $this->_error = new Error(Error::ERROR, 'connect', [
                    'file' => $exc->getFile(), 'line' => $exc->getLine()], [
                    'info' => $exc->errorInfo, 'message' => $exc->getMessage(), 'trace' => $exc->getTrace()]
                );
            }
        }
        return $this->_pdo;
    }

    /**
     * Map Query to internal SQL requests.
     * Make unified Result object.
     * 
     * @param Query $query
     * @return Result
     */
    public function execute(Query $query) {
        if ($this->open()) {
            $action = $query->action;
            return new Result($this->$action($query), $this->error());
        }
        return new Result(null, $this->error());
    }

    /**
     * Close connection
     */
    public function close() {
        // do nothing
    }

    /**
     * Get error
     * 
     * @return Error
     */
    public function error() {
        return $this->_error? : new Error(Error::SUCCESS);
    }

    /**
     * Raw query to PDO
     * 
     * @param string $sql
     * @param array $params
     * @return PDOStatement | null
     */
    private function query($sql, $params) {
        if (($statement = $this->_pdo->prepare($sql)) && ($statement->execute($params)) && ('00000' === $statement->errorCode())) {
            return $statement;
        }
        $exc = new \Exception();
        $this->_error = $statement ?
                new Error(Error::ERROR, 'execute', [], [
            'info' => $statement->errorInfo(),
            'message' => self::getMessage($statement->errorInfo()),
            'trace' => $exc->getTrace()
                ]) :
                new Error(Error::ERROR, 'prepare', [], [
            'info' => $this->_pdo->errorInfo(),
            'message' => self::getMessage($this->_pdo->errorInfo()),
            'trace' => $exc->getTrace()
        ]);
        return null;
    }

    /**
     * Database count
     * 
     * @param Query $query
     * @return Result
     */
    public function count(Query $query) {
        $sql = [
            'SELECT count(*) FROM ' . $query->tableName('`')
        ];
        $sql = array_merge($sql, $this->joins($query));
        $sql = array_merge($sql, $this->wheres($query));
        $sql = array_merge($sql, $this->havings($query));
        $sql = array_merge($sql, $this->limits($query));

        if ($result = $this->query(implode(' ', $sql), $query->getParams())) {
            $this->_error = null;
            $rec = $result->fetch(PDO::FETCH_NUM);
            return $rec[0];
        }

        return null;
    }

    public function delete(Query $query) {
        $sql = [
            'DELETE FROM ' . $query->tableName('`')
        ];
        $sql = array_merge($sql, $this->joins($query));
        $sql = array_merge($sql, $this->wheres($query));
        $sql = array_merge($sql, $this->havings($query));
        $sql = array_merge($sql, $this->limits($query));

        if ($result = $this->query(implode(' ', $sql), $query->getParams())) {
            return $result->rowCount();
        }

        return null;
    }

    public function insert(Query $query) {
        if (($fields = $this->fields($query)) && ($values = $query->getValues()) && ($acnt = count($values) / count($fields)) && (0 === (count($values) % count($fields)))) {
            $sql = [
                'INSERT INTO ' . $query->tableName('`'),
                '(' . implode(',', $fields) . ')',
                'VALUES',
            ];
            $vals = implode(',', array_fill(0, count($fields), '?'));
            $sql[] = '(' . implode('),(', array_fill(0, $acnt, $vals)) . ')';
            if ($result = $this->query(implode(' ', $sql), $values)) {
                return $result->lastInsertId();
            }
        }
        return null;
    }

    public function select(Query $query) {
        if (!($fields = $this->fields($query, true))) {
            $fields = ['*'];
        }
        $sql = [
            'SELECT',
            implode(',', $fields),
            'FROM ' . $query->tableName('`'),
        ];
        $sql = array_merge($sql, $this->joins($query));
        $sql = array_merge($sql, $this->wheres($query));
        $sql = array_merge($sql, $this->havings($query));
        $sql = array_merge($sql, $this->limits($query));

        if ($result = $this->query(implode(' ', $sql), $query->getParams())) {
            return $result->fetchAll(\PDO::FETCH_ASSOC);
        }

        return null;
    }

    public function update(Query $query) {
        if (($fields = $this->fields($query)) && ($values = $query->getValues()) && (count($values) == count($fields))) {
            $sql = [
                'UPDATE ' . $query->tableName('`'),
                'SET',
                implode('=?, ', $fields) . '=?'
            ];

            $sql = array_merge($sql, $this->joins($query));
            $sql = array_merge($sql, $this->wheres($query));
            $sql = array_merge($sql, $this->havings($query));
            $sql = array_merge($sql, $this->limits($query));

            if ($result = $this->query(implode(' ', $sql), array_merge($values, $query->getParams()))) {
                return $result->rowCount();
            }
        }
        return null;
    }

    public function joins(Query $query) {
        
    }

    public function wheres(Query $query) {
        
    }

    public function havings(Query $query) {
        
    }

    public function limits(Query $query) {
        
    }

    private function fields(Query $query, $aliased = false) {
        $fields = $query->getFields();
        return array_map(function(Field $field) use($aliased) {
            if ($field->operator) {
                // function
                $params = array_map(function($param) {
                    if ($param instanceof Field) {
                        return "`{$param->table}`.`{$param->name}`";
                    }
                    return $param;
                }, $field->parameters);
                return $field->name . '(' . implode(',', $params) . ')';
            }
            return "`{$field->table}`.`{$field->name}`" . ($aliased ? " AS `{$field->alias}`" : '');
        }, $fields);
    }
    
    private function fieldName(Field $field) {
        
    }

    private function fieldValue(Field $field) {
        
    }

}
