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

namespace Wtf\Traits;

/**
 * Description of Sql
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
trait Sql {

	static private $_sql92 = [
		'00' => [
			'000' => 'Success',
		],
		'01' => [
			'000' => 'Warning',
			'001' => 'Cursor operation conflict',
			'002' => 'Disconnect error',
			'003' => 'Null value eliminated in set function',
			'004' => 'Data truncated',
			'005' => 'Insufficient item descriptor areas',
			'006' => 'Privilege not revoked',
			'007' => 'Privilege not granted',
			'008' => 'Implicit zero-bit padding',
			'009' => 'Search expression too long for information schema',
			'00A' => 'Query expression too long for information schema',
			'S00' => 'Invalid connection string attribute',
			'S01' => 'Error in row',
			'S02' => 'Option value changed',
		],
		'02' => [
			'000' => 'No data',
		],
		'07' => [
			'000' => 'Dynamic SQL error',
			'001' => 'Wrong number of parameters',
			'002' => 'Mismatching parameters',
			'003' => 'Cursor specification cannot be executed',
			'004' => 'Missing parameters',
			'005' => 'Invalid cursor state',
			'006' => 'Restricted data type attribute violation',
			'007' => 'Using clause required for result fields',
			'008' => 'Invalid descriptor count',
			'009' => 'Invalid descriptor index',
		],
		'08' => [
			'000' => 'Connection exception',
			'001' => 'SQL-client unable to establish SQL-connection',
			'002' => 'Connection already in use',
			'003' => 'Connection does not exist',
			'004' => 'SQL-server rejected establishment of connection',
			'006' => 'Connection failure',
			'007' => 'Connection failure during transaction',
			'900' => 'Server lookup failed',
			'S01' => 'Communication link failure',
		],
		'0A' => [
			'000' => 'Feature not supported',
			'001' => 'Multiple server transactions',
		],
		'21' => [
			'000' => 'Cardinality violation',
			'001' => 'Multiple server transactions',
			'S01' => 'Insert value list does not match column list',
			'S02' => 'Degree of derived table does not match column list',
		],
		'22' => [
			'000' => 'Data exception',
			'001' => 'String data, right truncation',
			'002' => 'Null value, no indicator',
			'003' => 'Numeric value out of range',
			'005' => 'Error in assignment',
			'007' => 'Invalid datetime format',
			'008' => 'Date-time field overflow',
			'009' => 'Invalid time zone displacement value',
			'011' => 'Substring error',
			'012' => 'Division by zero',
			'015' => 'Internal field overflow',
			'018' => 'Invalid character value for cast',
			'019' => 'Invalid escape character',
			'021' => 'Character not in repertoire',
			'022' => 'Indicator overflow',
			'023' => 'Invalid parameter value',
			'024' => 'Unterminated C string',
			'025' => 'Invalid escape sequence',
			'026' => 'String data, length mismatch',
			'027' => 'Trim error',
		],
		'23' => [
			'000' => 'Integrity constraint violation',
		],
		'24' => [
			'000' => 'Invalid cursor state',
		],
		'25' => [
			'000' => 'Invalid transaction state',
			'S02' => 'Transaction is still active',
			'S03' => 'Transaction has been rolled back',
		],
		'26' => [
			'000' => 'Invalid SQL statement identifier',
		],
		'27' => [
			'000' => 'Triggered data change violation',
		],
		'28' => [
			'000' => 'Invalid authorization specification',
		],
		'2A' => [
			'000' => 'Syntax error or access rule violation in direct SQL statement',
		],
		'2B' => [
			'000' => 'Dependent privilege descriptors still exist',
		],
		'2C' => [
			'000' => 'Invalid character set name',
		],
		'2D' => [
			'000' => 'Invalid transaction termination',
		],
		'2E' => [
			'000' => 'Invalid connection name',
		],
		'33' => [
			'000' => 'Invalid SQL descriptor name',
		],
		'34' => [
			'000' => 'Invalid cursor name',
		],
		'35' => [
			'000' => 'Invalid condition number',
		],
		'37' => [
			'000' => 'Syntax error or access rule violation in dynamic SQL statement',
		],
		'3C' => [
			'000' => 'Duplicate cursor name',
		],
		'3F' => [
			'000' => 'Invalid schema name',
		],
		'40' => [
			'000' => 'Transaction rollback',
			'001' => 'Serialization failure',
			'002' => 'Transaction rollback: Integrity constraint violation',
			'003' => 'Transaction rollback: Statement completion unknown',
		],
		'42' => [
			'000' => 'Syntax error or access rule violation',
			'S01' => 'Base table or view already exists',
			'S02' => 'Base table or view not found',
			'S11' => 'Index already exists',
			'S12' => 'Index not found',
			'S21' => 'Column already exists',
			'S22' => 'Column not found',
			'S23' => 'No default for column',
		],
		'44' => [
			'000' => 'WITH CHECK OPTION violation',
		],
		'HY' => [
			'000' => 'General error',
			'001' => 'Storage allocation failure',
			'002' => 'Invalid column number',
			'003' => 'Invalid application buffer type',
			'004' => 'Invalid SQL Data type',
			'008' => 'Operation cancelled',
			'009' => 'Invalid use of null pointer',
			'010' => 'Function sequence error',
			'011' => 'Operation invalid at this time',
			'012' => 'Invalid transaction operation code',
			'015' => 'No cursor name avilable',
			'018' => 'Server declined cancel request',
			'090' => 'Invalid string or buffer length',
			'091' => 'Descriptor type out of range',
			'092' => 'Attribute or Option type out of range',
			'093' => 'Invalid parameter number',
			'095' => 'Function type out of range',
			'096' => 'Information type out of range',
			'097' => 'Column type out of range',
			'098' => 'Scope type out of range',
			'099' => 'Nullable type out of range',
			'100' => 'Uniqueness option type out of range',
			'101' => 'Accuracy option type out of range',
			'103' => 'Direction option out of range',
			'104' => 'Invalid precision or scale value',
			'105' => 'Invalid parameter type',
			'106' => 'Fetch type out of range',
			'107' => 'Row value out of range',
			'108' => 'Concurrency option out of range',
			'109' => 'Invalid cursor position',
			'110' => 'Invalid driver completion',
			'111' => 'Invalid bookmark value',
			'C00' => 'Driver not capable',
			'T00' => 'Timeout expired',
		],
		'HZ' => [
			'000' => 'RDA error',
			'010' => 'Access control violation',
			'020' => 'Bad repetition count',
			'080' => 'Resource not available',
			'090' => 'Resource already open',
			'100' => 'Resource unknown',
			'380' => 'SQL usage violation',
		],
		'IM' => [
			'000' => 'Internal error',
			'001' => 'Driver does not support this function',
			'002' => 'Data source name not found and no default driver specified',
			'003' => 'Specified driver could not be loaded',
			'004' => 'Driver\'s AllocEnv failed',
			'005' => 'Driver\'s AllocConnect failed',
			'006' => 'Driver\'s SetConnectOption failed',
			'007' => 'No data source or driver specified, dialog prohibited',
			'008' => 'Dialog failed',
			'009' => 'Unable to load translation DLL',
			'010' => 'Data source name too long',
			'011' => 'Driver name too long',
			'012' => 'DRIVER keyword syntax error',
			'013' => 'Trace file error',
		],
	];

	/**
	 * Translate SQL-92 code to human-readable message.
	 * 
	 * @param string $code 5-sym SQL-92 code
	 * @return string
	 */
	public static function getMessage($code) {
		$hi = substr($code, 0, 2);
		$lo = substr($code, 2, 3);
		if(isset(self::$_sql92[$hi])) {
			if(isset(self::$_sql92[$hi][$lo])) {
				return self::$_sql92[$hi]['000'] . ($lo ? ': ' . self::$_sql92[$hi][$lo] : '');
			}
			return self::$_sql92[$hi]['000'] . ": #{$lo}";
		}
		return "Unknown error #{$code}";
	}

}
