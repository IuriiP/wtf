<?php

/*
 * Copyright (C) 2017 IuriiP <hardwork.mouse@gmail.com>
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

/**
 * Description of Input
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Input implements \Wtf\Interfaces\ArrayAccessRead, \Iterator {

	use \Wtf\Traits\ArrayAccessRead;

	private static $_etalon = [
		'tmp_name' => null,
		'name' => null,
		'type' => null,
		'size' => 0,
		'error' => 0,
	];

	private $_data = [];

	/**
	 * Make the unified interface to input data.
	 * 
	 * @param string $method
	 * 
	 * For test purposes
	 * @param array|null $data
	 * @param array $files
	 */
	public function __construct($method, $data = null, $files = []) {
		switch($method) {
			case 'get':
				$this->_data = is_array($data) ? $data : $_GET;
				break;
			case 'post':
				$this->_data = is_array($data) ? $data : $_POST;
				$this->_registerFiles($this->_data, $files ? : $_FILES);
				break;
			default:
				$input = $data ? $data : file_get_contents('php://input');
				if(preg_match('~boundary=(.*)$~', $_SERVER['CONTENT_TYPE'], $matches)) {
					$this->_rfc1341($input, $matches[1]);
				} else {
					parse_str(urldecode($input), $this->_data);
				}
				break;
		}
	}

	/**
	 * Helper
	 * 
	 * @param string | \Wtf\Core\InputFile $object
	 * @return boolean
	 */
	public static function isFile($object) {
		return $object instanceof InputFile;
	}

	/**
	 * Get some/all elements.
	 * 
	 * @return array
	 */
	public function __invoke() {
		$list = [];
		$args = func_get_args();
		foreach($args as $arg) {
			if(is_array($arg)) {
				$list = array_merge($list, $arg);
			} else {
				$list[] = (string) $arg;
			}
		}
		return $list ? array_intersect_key($this->_data, array_flip($list)) : $this->_data;
	}

	/**
	 * Convert descriptors into objects.
	 * 
	 * @param array $target
	 * @param array $files
	 */
	private function _registerFiles(&$target, $files) {
		if(is_array($files)) {
			foreach($files as $key => $value) {
				if(array_intersect_key(self::$_etalon, $value) === self::$_etalon) {
					$target[$key] = new InputFile($value);
				} else {
					$this->_registerFiles($target[$key], $value);
				}
			}
		}
	}

	/**
	 * Multipart parsing RFC1341
	 * 
	 * @param string $input
	 * @param string $boundary
	 */
	private function _rfc1341($input, $boundary) {
		// split to blocks
		if($blocks = preg_split("/^-+{$boundary}[\\r\\n]*?(^|--$)/m", $input)) {
			// remove preamble and epilogue
			array_shift($blocks);
			array_pop($blocks);
			foreach($blocks as $block) {
				$this->_parse($block);
			}
		}
	}

	/**
	 * Block parsing
	 * 
	 * @param string $block
	 */
	private function _parse($block) {
		if(preg_match('~^((?:.|\s)*?)^[\r\n]+?(^(?:.|\s)*)$[\r\n]+?~m', $block, $parts)) {
			$header = $parts[1];
			$body = $parts[2];
			if(!empty($header) && preg_match('~^Content-Disposition:.*\s+name="(.+?)"[^"]+?(?:filename="(.+?)")?[^"]*?$~m', $header, $names)) {
				// there is a header with name(s)
				$name = $names[1];
				$filename = empty($names[2]) ? '' : $names[2];
				$this->_data = array_merge_recursive($this->_data, $filename ? $this->_file($name, $filename, $body, $header) : $this->_field($name, $body));
			} else {
				// skip w/o header/name
			}
		}
	}

	/**
	 * Store temp file and make descriptor.
	 * 
	 * @param string $name
	 * @param string $selfname
	 * @param string $body
	 * @param string $header
	 * @return array
	 */
	private function _file($name, $selfname, $body, $header) {
		$mime = preg_match('~^Content-Type:\s*(.+)$~m', $header, $matches) ? $matches[1] : 'application/octet-stream';
		$size = strlen($body);
		$temp = '';
		$cnt = 0;
		$error = UPLOAD_ERR_OK;
		if(\Wtf\Helper\Common::returnBytes(ini_get('upload_max_filesize')) < $size) {
			$error = UPLOAD_ERR_INI_SIZE;
		} elseif(!($temp = tempnam(sys_get_temp_dir(), 'upload'))) {
			$error = UPLOAD_ERR_NO_TMP_DIR;
		} elseif(false === ($cnt = file_put_contents($temp, $body, LOCK_EX))) {
			$error = UPLOAD_ERR_CANT_WRITE;
		} elseif($cnt !== $size) {
			$error = UPLOAD_ERR_PARTIAL;
		}
		$file = new InputFile([
			//$_FILES['userfile']['name'] Original file name
			'name' => $selfname,
			//$_FILES['userfile']['type'] Mime-type
			'type' => $mime,
			//$_FILES['userfile']['size'] Size
			'size' => $cnt,
			//$_FILES['userfile']['tmp_name'] Temp name
			'tmp_name' => $temp,
			//$_FILES['userfile']['error'] Error code
			'error' => $error
		]);

		return $this->_field($name, $file);
	}

	/**
	 * For resolving complex names
	 * 
	 * @param string $name
	 * @param string|array $value
	 * @return array
	 */
	private function _field($name, $value) {
		$xarray = [];
		if(is_string($value)) {
			$coded = urlencode($value);
			parse_str("{$name}={$coded}", $xarray);
		} else {
			parse_str("{$name}=0", $xarray);
			array_walk_recursive($xarray, function(&$point) use($value) {
				$point = $value;
			});
		}
		return $xarray;
	}

	/**
	 * Get by comlpicate name
	 * 
	 * @param string|array $offset
	 * @param array $list
	 * @return mixed
	 * @throws \ErrorException
	 */
	private function _complexOffset($offset, $list) {
		if(is_string($offset)) {
			$offset = array_filter(explode('/', $offset), function($val) {
				return $val !== '';
			});
		}
		if(is_array($offset) && is_array($list)) {
			$index = array_shift($offset);
			$index = is_numeric($index) ? (int) $index : $index;
			if(isset($list[$index])) {
				return $offset ? $this->_complexOffset($offset, $list[$index]) : [$list[$index]];
			}
		}
		return null;
		;
	}

	/**
	 * ArrayAccess implementation
	 */

	/**
	 * Check if offset exists
	 * 
	 * @param type $offset
	 * @return boolean
	 */
	public function offsetExists($offset) {
		return is_array($this->_complexOffset($offset, $this->_data));
	}

	/**
	 * Get element by complex name
	 * 
	 * @param string $offset
	 * @return mixed
	 */
	public function offsetGet($offset) {
		$ret = $this->_complexOffset($offset, $this->_data);
		return is_array($ret) ? reset($ret) : null;
	}

	/**
	 * Iterator implementation
	 */

	/**
	 */
	public function current() {
		return key($this->_data) !== null ? current($this->_data) : null;
	}

	public function key() {
		return key($this->_data);
	}

	public function next() {
		return next($this->_data);
	}

	public function rewind() {
		return reset($this->_data);
	}

	public function valid() {
		return key($this->_data) !== null;
	}

}
