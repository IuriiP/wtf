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
 * configuration as views
 * 
 * 'errors' => 'errors/%s.view'
 * 
 * or direct files
 * 
 * 'errors' = [
 *      404 => 'errors/404.html',
 *      ...
 *            ]
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Error extends \Wtf\Core\Response implements \Wtf\Interfaces\Configurable {

	use \Wtf\Traits\Configurable;

	private $_data = [];

	public function __construct($code, $data) {
		$this->_code = $code;
		$this->_data = $data;
	}

	public function __toString() {
		$cfg = self::configure('errors');
		if(is_string($cfg)) {
			return (string) \Wtf\Core\Response::view(sprintf($cfg, $this->_code), $this->_data);
		} elseif(is_array($cfg) && !empty($cfg[$this->_code])) {
			$res = \Wtf\Core\Resource::produce($cfg[$this->_code]);
			if($res->exists() && !$res->isContainer()) {
				return \Wtf\Helper\Common::vnsprintf($res->getContent(), $this->_data);
			}
			return $cfg[$this->_code];
		}

		return '';
	}

	public function clear() {
		$this->_data = [];

		return $this;
	}

}
