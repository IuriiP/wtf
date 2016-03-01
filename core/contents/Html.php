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
namespace Wtf\Core\Contents;

/**
 * Html treat its content as
 * \DOMDocument
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
class Html extends Wtf\Core\Content {

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
        if ($content instanceof \SimpleXMLElement) {
            $this->content = new DOMDocument('1.0', $charset? : 'utf-8');
            $this->content->importNode(dom_import_simplexml($content), true);
        } elseif ($content instanceof \DOMDocument) {
            $this->content = clone $content;
        } elseif (is_string($content)) {
            $this->content = new DOMDocument('1.0', $charset? : 'utf-8');
            $this->content->loadHTML($content, LIBXML_COMPACT | LIBXML_NONET);
        }
    }

    public function getMime() {
        return 'text/html; charset=' . $this->content->encoding;
    }

    public function getLength() {
        return null;
    }

    public function append($args) {
        return $this->assert($args[0],count($args)>1 ? $args[1] : self::ASSERT_END);
    }

    public function assert($assertion) {
        
    }
    
    public function send() {
        
    }
}
