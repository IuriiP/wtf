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
 * Description of Html
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Html extends \Wtf\Core\Response {

	/**
	 * @var \DOMDocument
	 */
	private $_dom = null;

	/**
	 *
	 * @var \DOMXPath
	 */
	private $_xpath = null;

	private $_head = null;

	private $_body = null;

	private $_footer = null;
	
	private $_options = [];

	/**
	 * Make from:
	 * SimpleXMLElement
	 * DOMDocument
	 * string
	 * 
	 * @param \DOMDocument|\SimpleXMLElement|string $content
	 * @param string $charset
	 */
	public function __construct($content, $charset = null) {
		if($content instanceof \SimpleXMLElement) {
			$this->_dom = new DOMDocument('1.0', $charset? : 'utf-8');
			$this->_dom->importNode(dom_import_simplexml($content), true);
		} elseif($content instanceof \DOMDocument) {
			$this->_dom = clone $content;
		} elseif($content) {
			$this->_dom = new DOMDocument('1.0', $charset? : 'utf-8');
			$this->_dom->loadHTML((string) $content, LIBXML_COMPACT | LIBXML_NONET);
		}
		if($this->_dom) {
			$this->_xpath = new \DOMXPath($this->_dom);
			$body = $this->_xpath->query('//body');
			if($body->length) {
				$this->_body = $body->item(0);
			} else {
				$this->_body = $this->_dom->documentElement->appendChild($this->_dom->createElement('body'));
			}
			$head = $this->_xpath->query('//head');
			if($head->length) {
				$this->_head = $head->item(0);
			} else {
				$this->_head = $this->_dom->documentElement->insertBefore($this->_dom->createElement('head'), $this->_body);
			}
			$footer = $this->_xpath->query('//footer');
			if($footer->length) {
				$this->_footer = $footer->item(0);
			} else {
				$this->_footer = $this->_body->appendChild($this->_dom->createElement('footer'));
			}
		}
		
	}

	public function __toString() {
		if(!empty($this->_options['xhtml'])) {
			$xml = $this->_dom->saveXML($this->_dom->documentElement);
			$text = "<!DOCTYPE html>\n{$xml}";
		}
		$text = $this->_dom->saveHTML();
		$this
			->header('Content-Type', 'text/html')
			->header('Content-Length', strlen($text));
		
		return $text;
	}

	public function clear() {
		$this->_dom = null;
		return $this;
	}

	public function options($arguments=[]) {
		$this->_options = array_replace($this->_options, $arguments);
		return $this;
	}

}
