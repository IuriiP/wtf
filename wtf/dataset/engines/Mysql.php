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

namespace Wtf\Dataset\Engines;

use Wtf\Helper\Complex;

/**
 * Mysql engine.
 * 
 * Implements SQL, CRUD.
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Mysql extends \Wtf\Dataset\Engine implements \Wtf\Interfaces\Sql, \Wtf\Interfaces\Pageable {

	use \Wtf\Traits\Sql;

	private $_mysqli = null;

	// Wtf\Dataset\Engine::
	/**
	 * Check ready state
	 * 
	 * Wtf\Dataset\Engine::isReady()
	 * 
	 * @return bool
	 */
	public function isReady() {
		return (bool) $this->_mysqli;
	}

	/**
	 * Open connection.
	 * 
	 * Wtf\Dataset\Engine::open()
	 * 
	 * ######## config
	 * # connection
	 * host=string
	 * dbname=string
	 * ### optional connect data
	 * port=int
	 * charset=string
	 * # credentials
	 * username=string
	 * password=string
	 * ### optional options 
	 * autocommit=bool
	 * prefetch=bool
	 * persistent=bool
	 * add_table_names=bool
	 * buffered_query=bool
	 * local_infile=bool
	 * read_default_file=bool
	 * read_default_group=bool
	 * direct_query=bool
	 * found_rows=bool
	 * ignore_space=bool
	 * compress=bool
	 * timeout=int
	 * max_buffer_size=int
	 * ssl_ca=string
	 * init_command=string
	 * ssl_capath=string
	 * ssl_cert=string
	 * ssl_cipher=string
	 * ssl_key=string
	 * errmode=silent|warning|exception
	 * case=natural|lower|upper
	 * nulls=natural|empty2null|null2empty
	 * ########
	 */
	public function open() {
		if(!$this->isReady()) {
			$dsn = Complex::arr2attr($this->config(['host', 'dbname', 'port', 'charset']));
			$cred = $this->config(['username', 'password']);
			try {
				$this->_mysqli = new \PDO('mysql:' . implode(';', $dsn), $cred['username'], $cred['password'], $this->options([
						PDO::ATTR_AUTOCOMMIT => 'autocommit',
						PDO::ATTR_PREFETCH => 'prefetch',
						PDO::ATTR_TIMEOUT => 'timeout',
						PDO::ATTR_PERSISTENT => 'persistent',
						PDO::ATTR_FETCH_TABLE_NAMES => 'add_table_names',
						PDO::ATTR_ERRMODE => ['errmode' => [
								'' => PDO::ERRMODE_SILENT,
								'silent' => PDO::ERRMODE_SILENT,
								'warning' => PDO::ERRMODE_WARNING,
								'exception' => PDO::ERRMODE_EXCEPTION,
							]],
						PDO::ATTR_CASE => ['case' => [
								'' => PDO::CASE_NATURAL,
								'natural' => PDO::CASE_NATURAL,
								'lower' => PDO::CASE_LOWER,
								'upper' => PDO::CASE_UPPER,
							]],
						PDO::ATTR_ORACLE_NULLS => ['nulls' => [
								'' => PDO::NULL_NATURAL,
								'natural' => PDO::NULL_NATURAL,
								'empty2null' => PDO::NULL_EMPTY_STRING,
								'null2empty' => PDO::NULL_TO_STRING,
							]],
						// MySQL specific
						PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => 'buffered_query',
						PDO::MYSQL_ATTR_LOCAL_INFILE => 'local_infile',
						PDO::MYSQL_ATTR_INIT_COMMAND => 'init_command',
						PDO::MYSQL_ATTR_READ_DEFAULT_FILE => 'read_default_file',
						PDO::MYSQL_ATTR_READ_DEFAULT_GROUP => 'read_default_group',
						PDO::MYSQL_ATTR_MAX_BUFFER_SIZE => 'max_buffer_size',
						PDO::MYSQL_ATTR_DIRECT_QUERY => 'direct_query',
						PDO::MYSQL_ATTR_FOUND_ROWS => 'found_rows',
						PDO::MYSQL_ATTR_IGNORE_SPACE => 'ignore_space',
						PDO::MYSQL_ATTR_COMPRESS => 'compress',
						PDO::MYSQL_ATTR_SSL_CA => 'ssl_ca',
						PDO::MYSQL_ATTR_SSL_CAPATH => 'ssl_capath',
						PDO::MYSQL_ATTR_SSL_CERT => 'ssl_cert',
						PDO::MYSQL_ATTR_SSL_CIPHER => 'ssl_cipher',
						PDO::MYSQL_ATTR_SSL_KEY => 'ssl_key',
				]));
			} catch(Exception $exc) {
				echo $exc->getTraceAsString();
			}
		}
		return (bool) $this->_mysqli;
	}

	public function close() {
		
	}

	public function error() {
		
	}

	public function count($conditions) {
		
	}

	public function create($data) {
		
	}

	public function delete($conditions) {
		
	}

	public function fields(\Wtf\Dataset\Query $query) {
		
	}

	public function havings(\Wtf\Dataset\Query $query) {
		
	}

	public function joins(\Wtf\Dataset\Query $query) {
		
	}

	public function limits(\Wtf\Dataset\Query $query) {
		
	}

	public function paginate($pagesize) {
		
	}

	public function page($pageno) {
		
	}

	public function read($data, $conditions) {
		
	}

	public function sqlDelete(\Wtf\Dataset\Query $query) {
		
	}

	public function sqlInsert(\Wtf\Dataset\Query $query) {
		
	}

	public function sqlSelect(\Wtf\Dataset\Query $query) {
		
	}

	public function sqlUpdate(\Wtf\Dataset\Query $query) {
		
	}

	public function touch(\Wtf\Dataset\Query $query) {
		
	}

	public function update($data, $conditions) {
		
	}

	public function wheres(\Wtf\Dataset\Query $query) {
		
	}

	public static function getAttributes(\Wtf\Dataset\Data $data) {
		$ret = [];
		foreach($data as $key => $value) {
			$ret[] = $value ? $key . '(' . implode(',', $value) . ')' : $key;
		}
		return implode(' ', $ret);
	}

}
