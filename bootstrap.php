<?php
/**
 * This file provide the bootstraping logic 
 * as set of the any callables.
 */
return [
	//
	// REQUIRED!
	//
	// predifined system contracts to singletons
	'server' => \Wtf\Core\Server::singleton(),
	// config path must be defined
	'config' => \Wtf\Core\Config::singleton(defined('CONFIG')?CONFIG:\Wtf\Core\Server::singleton()->document_root.'/config.ini'),
	//
	// Optional
	//
//	'events' => Wtf\Core\EventManager::singleton(),
	'session' => Wtf\Core\Session::singleton(),
	'logger' => Wtf\Core\Logger::singleton(),
	'profiler' => Wtf\Core\Profiler::singleton(),
	'trashbin' => Wtf\Core\Trashbin::singleton(),
	// self registered contracts if need
];
