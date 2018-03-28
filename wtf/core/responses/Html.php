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
class Html extends \Wtf\Core\Response implements \Wtf\Interfaces\Configurable {

	use \Wtf\Traits\Configurable;

	/**
	 * @var \DOMDocument
	 */
	protected $_dom = null;

	private $_asXhtml = false;

	private $_injections = [];

	private $_targets = [
		'head' => null,
		'body' => null,
		'footer' => null,
	];

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
			$this->_dom = new DOMDocument('1.0', $charset ?: 'utf-8');
			$this->_dom->appendChild($this->_dom->importNode(dom_import_simplexml($content), true));
		} elseif($content instanceof \DOMDocument) {
			$this->_dom = clone $content;
		} elseif($content) {
			$this->_dom = new DOMDocument('1.0', $charset ?: 'utf-8');
			$this->_dom->loadHTML((string) $content, LIBXML_COMPACT | LIBXML_NONET);
		}

		$this->_init();
	}

	private function _init() {
		if(!$this->_dom) {
			$this->_dom = new DOMDocument('1.0', 'utf-8');
		}

		$xpath = new \DOMXPath($this->_dom);
		$body = $xpath->query('//body');
		if($body->length) {
			$this->_targets['body'] = $body->item(0);
		} else {
			$this->_targets['body'] = $this->_dom->documentElement->appendChild($this->_dom->createElement('body'));
		}
		$head = $xpath->query('//head');
		if($head->length) {
			$this->_targets['head'] = $head->item(0);
		} else {
			$this->_targets['head'] = $this->_dom->documentElement->insertBefore($this->_dom->createElement('head'), $this->_body);
		}
		$footer = $xpath->query('//footer');
		if($footer->length) {
			$this->_targets['footer'] = $footer->item(0);
		} else {
			$this->_targets['footer'] = $this->_body->appendChild($this->_dom->createElement('footer'));
		}

		return $this;
	}

	/**
	 * Switch output type
	 * 
	 * @param boolean $onoff
	 * @return $this
	 */
	public function asXhtml($onoff = true) {
		$this->_asXhtml = $onoff;
		return $this;
	}

	/**
	 * Magic convert
	 * 
	 * @return string
	 */
	public function __toString() {
		if($this->_injections) {
			$depends = array_intersect_key($this->configure('dependencies'), $this->_injections);
			foreach($depends as $key => $injection) {
				if(isset($injection['tag'])) {
					$data = (array) $this->_injections[$key];
					$attrs = isset($injection['attr']) ? array_intersect_key($data, array_flip($injection['attr'])) : null;
					$cdata = isset($injection['data']) ? \Wtf\Helper\Common::vnsprintf($injection['data'], $data) : null;
					$this->_inject($injection['tag'], $attrs, $cdata, isset($injection['target']) ? $injection['target'] : 'body');
				}
			}
		}
		if($this->_asXhtml) {
			$text = '<!DOCTYPE html>\n' . $this->_dom->saveXML($this->_dom->documentElement);
			$this->header('Content-Type', 'text/xhtml+xml');
		} else {
			$text = $this->_dom->saveHTML();
			$this->header('Content-Type', 'text/html');
		}
		$this->header('Content-Length', strlen($text));

		return $text;
	}

	/**
	 * Reset all
	 * 
	 * @return $this
	 */
	public function clear() {
		$this->_dom = null;
		$this->_injections = [];
		$this->_asXhtml = false;

		return $this->init();
	}

	/**
	 * Inject part of HTML
	 * 
	 * @param \SimpleXMLElement | \DOMnode | string $fragment
	 * @return $this
	 */
	public function html($fragment) {
		if($fragment instanceof \SimpleXMLElement) {
			$sxe = $fragment;
		} elseif($fragment instanceof \DOMNode) {
			$sxe = simplexml_import_dom($fragment);
		} elseif($fragment && is_string($fragment)) {
			$sxe = simplexml_load_string($fragment);
		} else {
			$sxe = null;
		}

		if($sxe) {
			$name = $sxe->getName();
			if('html' === $name) {
				return $this->_append('head', $sxe->head)
						->_append('body', $sxe->body)
						->_append('footer', $sxe->footer);
			}
			return $this->_append($name, $sxe);
		}

		return $this;
	}

	/**
	 * Append source into target
	 * 
	 * @param string $name
	 * @param \SimpleXMLElement $source
	 * @return $this
	 */
	private function _append($name, \SimpleXMLElement $source) {
		if($source->count()) {
			if(isset($this->_targets[$name])) {
				$target = $this->_targets[$name];
				foreach($source->children() as $node) {
					$target->appendChild($this->_dom->importNode(dom_import_simplexml($node)));
				}
			} else {
				$this->_targets['body']->appendChild($this->_dom->importNode(dom_import_simplexml($source)));
			}
		}

		return $this;
	}

	private function _inject($tag, $attrs, $data, $name) {
		$elm = $this->_dom->createElement($tag);
		if($attrs) {
			foreach($attrs as $key => $val) {
				$elm->setAttribute($key, $val);
			}
		}
		if($data) {
			$elm->appendChild($this->_dom->createCDATASection($data));
		}

		if(isset($this->_targets[$name])) {
			$this->_targets[$name]->appendChild($elm);
		} else {
			$this->_targets['body']->appendChild($elm);
		}

		return $this;
	}

	/**
	 * Inject named dependancy
	 * 
	 * @param type $name
	 */
	public function inject($name, $data = []) {
		$this->_injections[$name] = $data;

		return $this;
	}

	/**
	 * Inject link to js into head
	 * 
	 * @param array $attrs
	 * @return $this
	 */
	public function js($attrs = []) {
		return $this->_inject('script', $attrs, null, 'head');
	}

	/**
	 * Inject direct script text into footer
	 * 
	 * @param string $data
	 * @return $this
	 */
	public function script($data) {
		return $this->_inject('script', null, $data, 'footer');
	}

	/**
	 * Inject link to css into head
	 * 
	 * @param array $attrs
	 * @return $this
	 */
	public function css($attrs = []) {
		$attrs['rel'] = 'stylesheet';
		return $this->_inject('link', $attrs, null, 'head');
	}

	/**
	 * Inject direct style into head
	 * 
	 * @param string $data
	 * @return $this
	 */
	public function style($data) {
		return $this->_inject('style', null, $data, 'head');
	}

	/**
	 * Inject link into head
	 * 
	 * @param array $attrs
	 * @return $this
	 */
	public function link($attrs = []) {
		return $this->_inject('link', $attrs, null, 'head');
	}

}
