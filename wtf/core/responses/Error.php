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

	private $_resource = null;
	private $_data = [];
	
	public function __construct($code, $data) {
		$this->_data = $data;
		$config = self::configure('errors');
		$path = $this->config('path');
		if($path) {
			$formats = $this->config('formats')?:['%s.html'];
			foreach($formats as $format) {
				$res = sprintf($format, $code);
				$resource = \Wtf\Core\Resource::produce($path,$res);
				if($resource->exists()) {
					$this->_resource = $resource;
					return;
				}
			}
		} elseif($res=$this->config($code)) {
				$resource = \Wtf\Core\Resource::produce($path,$res);
				if($resource->exists()) {
					$this->_resource = $resource;
					return;
				}
		}
	}

}
