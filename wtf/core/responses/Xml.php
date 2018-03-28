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

namespace Wtf\Core\Responses;

/**
 * Description of Xml
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Xml extends Json {

	public function __toString() {
		$text = \Wtf\Helper\Complex::arr2xml($this->_data)
			->asXML();
		$this->headers([
			'Content-Type' => 'text/xml',
			'Content-Length' => strlen($text),
		]);
		return $text;
	}

	public function xml($data) {
		$this->_data = array_merge_recursive($this->_data, $data);
	}

}
