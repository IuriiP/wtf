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

use Wtf\Core\Content;

/**
 * Chainable Response
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
class Response implements \Wtf\Interfaces\Bootstrap {

    /**
     * @var array HTTP/1.1 response codes
     */
    private static $_http = [
        //1xx: Informational:
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        //2xx: Success:
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        226 => 'IM Used',
        //3xx: Redirection:
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        //4xx: Client Error:
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        434 => 'Requested host unavailable',
        444 => '',
        449 => 'Retry With',
        451 => 'Unavailable For Legal Reasons',
        //5xx: Server Error:
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];

    /**
     * @var array of headers
     */
    private $_headers = [];

    /**
     * @var int response code
     */
    private $_code = 200;

    /**
     * @var \Wtf\Core\Content
     */
    private $_content = null;

    /**
     * @var \Wtf\Core\Content[]
     */
    private $_asserts = [];

    /**
     * Contracted name
     */
    public static function bootstrap() {
        App::contract('response', __CLASS__);
    }

    /**
     * Set headers array.
     * 
     * Clears headers when NULL or omitted.
     * Expands headers from array.
     * 
     * @param array $array
     * @return \Wtf\Core\Response
     */
    public function headers($array = null) {
        if (!headers_sent()) {
            if (null === $array) {
                $this->_headers = [];
            } else {
                $this->_headers = array_merge($this->_headers, (array) $array);
            }
        }
        return $this;
    }

    /**
     * SEt/replace the header.
     * 
     * @param string $name
     * @param mixed $value
     * @return \Wtf\Core\Response
     */
    public function header($name, $value = null) {
        if (!headers_sent()) {
            $this->_headers[$name] = $value;
        }
        return $this;
    }

    /**
     * Set response code.
     * 
     * @param int $code
     * @return \Wtf\Core\Response
     */
    public function code($code) {
        $this->_code = $code;
        return $this;
    }

    public function assert($content, $name = null, $position = 0) {
        $assertion = Content::make('assert', $content, $position);

        if ((Content::ASSERT_HERE === $position) && !($name && isset($this->_asserts[$name]))) {
            if ($this->_content) {
                if (method_exists($this->_content, 'assert')) {
                    $this->_content->assert($assertion, Content::ASSERT_END);
                }
            } else {
                $this->_content = $assertion;
            }
            if ($name) {
                $this->_asserts[$name] = null;
            }
        } elseif ($name) {
            $this->_asserts[$name] = $assertion;
        } else {
            $this->_asserts[] = $assertion;
        }
    }

    /**
     * Magic setter for any type casting.
     * 
     * @param type $type
     * @param type $args
     * @return \Wtf\Core\Response
     */
    public function __call($type, $args) {
        if ((count($args) < 1) || (null === $args[0])) {
            // clear
            $this->_content = null;
        } elseif (!$this->_content) {
            // initial
            $this->_content = Content::factory($type, $args);
        } elseif ($this->_content->isType($type)) {
            // mixing not allowed
            trigger_error(__CLASS__ . "::{$type}: content mixing not allowed to " . $this->_content->getType());
        } else {
            // expand if possible
            if (!$this->_content->append($args)) {
                trigger_error(__CLASS__ . "::{$type}: content expanding not allowed");
            }
        }
        return $this;
    }

    public function redirect($url, $code) {
        $this->clear()->header('Location', $url)->code($code? : 301);
        $this->send();
    }

    private function _sendHeader($code) {
        header("HTTP/1.1 {$code} " . self::$_http[$code], true);

        foreach ($this->_headers as $key => $value) {
            if (null !== $value) {
                if (is_array($value)) {
                    foreach ($value as $subval) {
                        header("{$key}: $subval", false);
                    }
                } else {
                    header("{$key}: $value", true);
                }
            }
        }
        return ($code >= 200) && ($code < 300);
    }

    public function send($trash = null) {
        if (!headers_sent() && $this->_sendHeader($this->_code) && $this->_content) {
            header('Content-type: ' . $this->_content->getMime(), true);

            if($trash) {
                if ($this->_content->isType('html')) {
                    $this->assert(Content::make('html_comment', $trash), '', Content::ASSERT_END);
                } else {
                    $this->assert(Content::make('http_header', $trash), '', Content::ASSERT_END);
                }
            }
            if ($this->_asserts && method_exists($this->_content, 'assert')) {
                foreach (array_filter($this->_asserts) as $assertion) {
                    $this->_content->assert($assertion);
                }
            }
            $this->_content->send();
        }
    }

}
