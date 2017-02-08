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

namespace Wtf\Core;

/**
 * Auth performs the authentification of the client.
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Auth implements \Wtf\Interfaces\Singleton, \Wtf\Interfaces\Configurable {

	use \Wtf\Traits\Singleton,
	 \Wtf\Traits\Configurable;

	protected $user = null;

	private function __construct() {
		$session = Session::singleton();
		$server = Server::singleton();

		$fp = $session['fingerprint'];
		if($fp) {
			if($server['remote_addr'] . $server['http_user_agent'] === $fp) {
				$this->user = $session['user'];
			} else {
				$session['fingerprint'] = '';
			}
		}
	}

	public function check() {
		return !!$this->user;
	}

	public function user() {
		return $this->user;
	}

	public function login($cred) {
		$session = Session::singleton();
		$server = Server::singleton();

		$this->user = new User($cred);

		$session['user'] = $this->user;
		$session['fingerprint'] = $server['remote_addr'] . $server['http_user_agent'];

		return $this;
	}

	public function logout() {
		$this->user = null;

		$session['user'] = $this->user;
		$session['fingerprint'] = '';

		return $this;
	}

	public function recover($data) {
		User::recover($data);

		return $this;
	}

}
