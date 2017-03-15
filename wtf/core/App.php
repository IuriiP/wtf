<?php

/*
 * Copyright (C) 2016 Iurii Prudius <hardwork.mouse@gmail.com>
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

use Wtf\Helper\Common;

/**
 * Main Application Class
 * 
 * @interface Contractor
 * @interface Singleton
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
class App implements \Wtf\Interfaces\Contractor, \Wtf\Interfaces\Singleton {

	use \Wtf\Traits\Contractor,
	 \Wtf\Traits\Singleton;

// Use for profiling purposes
	static private $_timer_stack = [];

	/**
	 * Clear output buffers into trashbin and turn off buffering
	 * 
	 * @param object $trashbin
	 */
	private function _clear($trashbin = null) {
		$trash = [];
		while(FALSE !== ($str = ob_get_clean())) {
			if($str) {
				array_unshift($trash, $str);
			}
		}

		if($trashbin) {
			$trashbin($trash);
		}
	}

	/**
	 * Process application
	 * 
	 * @param double $start_time Real start time
	 */
	public static function run($start_time) {
		ob_start();
		self::$_timer_stack[] = $start_time;

		/**
		 * @var \Wtf\Core\App Application get self instance
		 */
		$self = self::singleton();

		/**
		 * Contracts
		 */
		if(defined('BOOTSTRAP')) {
			self::$_contracts = Common::parsePhp(Resource::produce(BOOTSTRAP)->getContent());
		}

		// get path only from uri
		$uri = $self->server('request_uri');
		list($path, $q) = explode('?', $uri . '?', 2);

		// incapsulate request 
		$request = new Request();
		$request
			->format(pathinfo($path, PATHINFO_EXTENSION))
			->method($self->server('request_method'))
			->input(new Input($request->method))
			->accept($self->server('http_accept'))
			->language($self->server('http_accept_language'))
			->charset($self->server('http_accept_charset'))
			->encoding($self->server('http_accept_encoding'));
		$response = null;

		try {
			// find rule
			$rule = Rule::find($self->config('routes'), $path, $request->method);
			if($rule) {
				$response = $rule->execute($request);
			} else {
				$response = Response::error(404, ['path' => $path, 'method' => $request->method]);
			}
		} catch(Exception $e) {
			$response = Response::error(405, ['path' => $path, 'method' => $request->method]);
		}


//		self::x_debug($_SERVER, 'SERVER');
//		self::x_debug($self, 'App');
//		self::x_echo('Done!');

		if(!$response->sent) {
			$trashbin = $self->trashbin;
			// clear output
			$self->_clear($trashbin);
			// send Response
			$self->response->send($trashbin ? $trashbin() : []);
		}
	}

	/**
	 * Push current microtime to the stack
	 * 
	 * @return double Current microtime
	 */
	public static function startTimer() {
		return self::$_timer_stack[] = microtime(true);
	}

	/**
	 * Pop last microtime from stack
	 * 
	 * @return double Difference between current microtime & last stored
	 */
	public static function getTimer() {
		return microtime(true) - array_pop(self::$_timer_stack);
	}

	/**
	 * Calculate total microtime from first stored
	 * 
	 * @return double Total microtime
	 */
	public static function getTimerTotal() {
		return microtime(true) - reset(self::$_timer_stack);
	}

	public static function x_debug($var, $prefix = null) {
		ob_start();
		if($prefix) {
			echo "{$prefix}:\n";
		}
		var_export($var);
	}

	public static function x_echo() {
		ob_start();
		echo sprintf(...func_get_args());
	}

}
