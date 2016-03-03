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
class Html extends \Wtf\Core\Content
{

    /**
     * Early prepared xPath
     * 
     * @var \DOMXPath
     */
    private $xpath = null;

    /**
     * Early prepared <head> element
     * 
     * @var \DOMElement
     */
    private $head = null;

    /**
     * Early prepared <body> element
     * 
     * @var \DOMElement
     */
    private $body = null;

    /**
     * Make from:
     * SimpleXMLElement
     * DOMDocument
     * string
     * 
     * @param \DOMDocument|\SimpleXMLElement|string $content
     * @param string $charset
     */
    public function __construct($content, $charset = null)
    {
        parent::__construct(null,'html');

        if ($content instanceof \SimpleXMLElement) {
            $this->content = new DOMDocument('1.0', $charset? : 'utf-8');
            $this->content->importNode(dom_import_simplexml($content), true);
        } elseif ($content instanceof \DOMDocument) {
            $this->content = clone $content;
        } elseif (is_string($content)) {
            $this->content = new DOMDocument('1.0', $charset? : 'utf-8');
            $this->content->loadHTML($content, LIBXML_COMPACT | LIBXML_NONET);
        }
        if ($this->content) {
            $this->xpath = new \DOMXPath($this->content);
            $this->head = $this->xpath->query('//head')->item(0);
            $this->body = $this->xpath->query('//body')->item(0);
        }
    }

    public function getMime()
    {
        return 'text/html; charset=' . $this->content->encoding;
    }

    public function getLength()
    {
        return null;
    }

    public function __toString()
    {
        if ($this->content->documentElement->hasAttribute('xhtml')) {
            $this->content->documentElement->removeAttribute('xhtml');
            $xml = $this->content->saveXML($this->content->documentElement);
            return "<!DOCTYPE html>\n{$xml}";
        }
        return $this->content->saveHTML();
    }

    /**
     * Inject HTML (DOMDocument).
     * 
     * <head> always appended to existed
     * <body> injection depends to $position
     * 
     * @param \Wtf\Core\Contents\Wtf\Core\Contents\Html $content
     * @param int $position
     * @return boolean
     */
    protected function inject_html(Wtf\Core\Contents\Html $content, $position)
    {
        /**
         * @var \DOMDocument Description
         */
        $dom = $this->content;

        // <head> always appends to the end
        foreach ($content->content->getElementsByTagName('head')->item(0)->childNodes as $node) {
            $this->head->appendChild($dom->importNode($node, true));
        }
        // copy <body>
        $anchor = self::INJECT_BEGIN === $position ? $this->body->firstChild : null;
        foreach ($content->content->getElementsByTagName('body')->item(0)->childNodes as $node) {
            $this->body->insertBefore($dom->importNode($node, true), $anchor);
        }
        return true;
    }

    /**
     * Direct injection of the script.
     * 
     * INJECT_BEGIN append script to the <head>
     * INJECT_END append script to the <body>
     * 
     * @param \Wtf\Core\Contents\Wtf\Core\Contents\Script $content
     * @param int $position
     * @return boolean
     */
    protected function inject_script(Wtf\Core\Contents\Script $content, $position)
    {
        /**
         * @var \DOMDocument Description
         */
        $dom = $this->content;
        $target = self::INJECT_BEGIN === $position ? $this->head : $this->body;
        $target->appendChild($dom->createElement('script', (string) $content));
        return true;
    }

    /**
     * Direct injection of the style.
     * 
     * @param \Wtf\Core\Contents\Wtf\Core\Contents\Style $content
     * @param int $position
     * @return boolean
     */
    protected function inject_style(Wtf\Core\Contents\Style $content, $position)
    {
        /**
         * @var \DOMDocument Description
         */
        $dom = $this->content;
        $this->head->appendChild($dom->createElement('style', (string) $content));
        return true;
    }

    /**
     * Inject link to script
     * 
     * @param \Wtf\Core\Contents\Wtf\Core\Contents\Js $content
     * @param int $position
     * @return boolean
     */
    protected function inject_js(Wtf\Core\Contents\Js $content, $position)
    {
        /**
         * @var \DOMDocument Description
         */
        $dom = $this->content;
        if (self::INJECT_BEGIN === $position) {
            $links = $this->xpath->query('script[@src]');
            if ($links && ($count = $links->length)) {
                $anchor = $links->item(0);
                $target = $anchor->parentNode;
            } else {
                $target = $this->head;
                $anchor = null;
            }
        } else {
            $anchor = null;
            $target = $this->body;
        }
        $node = $dom->createDocumentFragment();
        if($node->appendXML((string) $content)) {
            $target->insertBefore($node,$anchor);
        }
        return true;
    }

    /**
     * Inject link to css
     * 
     * @param \Wtf\Core\Contents\Wtf\Core\Contents\Css $content
     * @param int $position
     * @return boolean
     */
    protected function inject_css(Wtf\Core\Contents\Css $content, $position)
    {
        /**
         * @var \DOMDocument Description
         */
        $dom = $this->content;
        $links = $this->xpath->query('link[@rel = "stylesheet"]');
        if ($links && ($count = $links->length)) {
            $anchor = self::INJECT_BEGIN === $position ? $links->item(0) : $links->item($count - 1)->nextSibling;
        } else {
            $anchor = null;
        }
        $node = $dom->createDocumentFragment();
        if($node->appendXML((string) $content)) {
            $this->head->insertBefore($node,$anchor);
        }
        return true;
    }

    /**
     * 
     * 
     * @param \Wtf\Core\Contents\Wtf\Core\Contents\Link $content
     * @param int $position
     * @return boolean
     */
    protected function inject_link(Wtf\Core\Contents\Link $content, $position)
    {
        /**
         * @var \DOMDocument Description
         */
        $dom = $this->content;
        $links = $this->xpath->query('link');
        if ($links && ($count = $links->length)) {
            $anchor = self::INJECT_BEGIN === $position ? $links->item(0) : $links->item($count - 1)->nextSibling;
        } else {
            $anchor = null;
        }
        $node = $dom->createDocumentFragment();
        if($node->appendXML((string) $content)) {
            $this->head->insertBefore($node,$anchor);
        }
        return true;
    }

    /**
     * Append debug information to the HTTP header
     * 
     * @param \Wtf\Core\Contents\Wtf\Core\Contents\HttpDebug $content
     * @param int $position
     * @return boolean
     */
    protected function inject_http_debug(Wtf\Core\Contents\HttpDebug $content, $position)
    {
        foreach ($content as $value) {
            header('X-Debug-String: ' . $value, false);
        }
        return true;
    }

    /**
     * Append final comment.
     * 
     * @param \Wtf\Core\Contents\Wtf\Core\Contents\HtmlComment $content
     * @param type $position
     * @return boolean
     */
    protected function inject_html_comment(Wtf\Core\Contents\HtmlComment $content, $position)
    {
        $dom = $this->content;
        $comment = $dom->createComment((string) $content);
        if (self::INJECT_END === $position) {
            // after body!
            $this->body->parentNode->appendChild($comment);
        } else {
            // last in body
            $this->body->appendChild($comment);
        }
        return true;
    }

}
