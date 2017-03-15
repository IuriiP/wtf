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

namespace Wtf\Core\Responses;

/**
 * Description of Error
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Error extends \Wtf\Core\Response implements \Wtf\Interfaces\Configurable {

	use \Wtf\Traits\Configurable;

	/**
	 * @var \Wtf\Core\Resource
	 */
	private $_code = 0;

	private $_data = [];

	public function __construct($code, $data) {
		self::configure('errors');
		$this->_code = $code;
		$this->_data = $data;
		
		$views = $this->config('views');
		if($views) {
			$text = (string) \Wtf\Core\Response::view(sprintf($views,$code),$data);
			if($text) {
				return $text;
			}
		}

		$err = $this->config((string) $code);
		if($err) {
			$res = \Wtf\Core\Resource::produce($err);
			if($res->exists()) {
				return
			}
		}
		$path = $this->config('path');
		$this->code($code);
		if($path) {
			$formats = $this->config('format')? : ['%s.html'];
			foreach($formats as $format) {
				$res = sprintf($format, $code);
				$resource = \Wtf\Core\Resource::produce($path, $res);
				if($resource->exists()) {
					$this->_resource = $resource;
					return;
				}
			}
		} elseif($res = $this->config($code)) {
			$resource = \Wtf\Core\Resource::produce($res);
			if($resource->exists()) {
				$this->_resource = $resource;
			} else {
				$this->_content = $res;
			}
			return;
		}
	}

	public function __toString() {
		return \Wtf\Helper\Common::includePhp($this->_resource?$this->_resource->getContent():$this->_content, $this->_data);
	}

	public function clear() {
		$this->_resource = null;
		$this->_data = [];

		return $this;
	}

}
