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
 * @interface Container
 * @interface Singleton
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
class App implements \Wtf\Interfaces\Container, \Wtf\Interfaces\Singleton {

	use \Wtf\Traits\Container,
	 \Wtf\Traits\Singleton;

// Use for profiling purposes
	static private $_timer_stack = [];

	/**
	 * Set default contracts:
	 * server
	 * path
	 * config
	 * 
	 */
	private function __construct() {
		
	}

	/**
	 * Register the contract for
	 * the Class or
	 * the created singletone object.
	 * 
	 * Return the callable for the instance bootstrap method.
	 * 
	 * @param type $name
	 * @param type $instance
	 * @return type
	 */
	static public function contract($name, $instance) {
		$self = self::singleton();
		if(!$self->offsetExists($name)) {
			$self->offsetSet($name, $instance);
		}
		return $self[$name];
	}

	static private function _performBoot(\ReflectionClass $ref) {
		if($ref->implementsInterface('\\Wtf\\Interfaces\\Bootstrap')) {
			return $ref->getMethod('bootstrap')->invoke(null, self::singleton());
		}
		return null;
	}

	static private function _makeBooting($bootstrap) {
		foreach($bootstrap as $key => $value) {
			if(is_numeric($key)) {
				self::_performBoot(new \ReflectionClass($value));
			} elseif(is_object($value)) {
				self::contract($key, $value);
			} elseif(is_string($value)) {
				$ref = new \ReflectionClass($value);
				self::contract($key, self::_performBoot($ref)? : $ref->newInstance());
			}
		}
	}

	/**
	 * Process application
	 * 
	 * @return boolean FALSE prevent any output
	 */
	static public function run($start_time) {
		ob_start();
		self::$_timer_stack[] = $start_time;

		/**
		 * @var \Wtf\Core\App Application get self instance
		 */
		$self = self::singleton();

		/**
		 * Bootstraping
		 */
		$boot = getenv('BOOTSTRAP');
		if($boot) {
			self::_makeBooting(Common::parsePhp(Resource::produce($boot)->getContent()));
		}

		/**
		 * @var \Wtf\Core\Request make default request from $_SERVER
		 */
		$self->request = new Request(Server('request_uri'));

		/**
		 * @var \Wtf\Core\Response execute request (recursive) and get response
		 */
		$self->response = $self->request->execute(Server('request_method'));

		if(!$self->response->sent) {
			/**
			 * Clear output buffers into trashbin
			 */
			$trash = [];
			while(FALSE !== ($str = ob_get_clean())) {
				if($str) {
					$trash[] = $str;
				}
			}
			if($trash && $self->trashbin) {
				$self->trashbin($trashbin);
			}

			// Send Response
			$self->response->send();
		}
	}

	/**
	 * Push current microtime to the stack
	 * 
	 * @return double Current microtime
	 */
	static public function startTimer() {
		return self::$_timer_stack[] = microtime(true);
	}

	/**
	 * Pop last microtime from stack
	 * 
	 * @return double Difference between current microtime & last stored
	 */
	static public function getTimer() {
		return microtime(true) - array_pop(self::$_timer_stack);
	}

	/**
	 * Calculate total microtime from first stored
	 * 
	 * @return double Total microtime
	 */
	static public function getTimerTotal() {
		return microtime(true) - reset(self::$_timer_stack);
	}

}
