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

namespace Wtf\Core\Resources;

/**
 * Description of File
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
class File extends \Wtf\Core\Resource implements \Wtf\Interfaces\Readable, \Wtf\Interfaces\Writable {

    private $_origin = null;
    private $_content = null;

    /**
     * @param string $path path to file
     * @param array $options file options
     */
    public function __construct($path, $options = []) {
        // $data ignored!
        $this->_origin = realpath($path);
        $this->_opt = $options;
    }

    /**
     * Check if is directory
     * 
     * @return boolean
     */
    public function isContainer() {
        return is_dir($this->_origin);
    }

    /**
     * Get scheme = `file://`
     * 
     * @return string
     */
    public function getScheme() {
        return 'file://';
    }

    /**
     * Get the file from dir
     * 
     * @param string $name
     * @return File
     */
    public function child($name) {
        if (is_dir($this->_origin)) {
            return new File($this->_origin . ($name ? DIRECTORY_SEPARATOR . $name : ''), $this->_opt);
        }
        return null;
    }

    /**
     * Get the object's container
     * 
     * @return \Wtf\Core\Resource
     */
    public function container() {
        $cont = dirname($this->_origin);
        return new File($cont, []);
    }

    /**
     * Get the specified timestamp of file:
     * 'c' - create time
     * 'm' - modify time  (default)
     * 'a' - access time
     * 
     * @param string $type
     * @return int
     */
    public function getTime($type = null) {
        switch (strtolower($type)) {
            case 'c':
                return filectime($this->_origin);
            case 'a':
                return fileatime($this->_origin);
        }
        return filemtime($this->_origin);
    }

    /**
     * Get the full path of the file
     * 
     * @return string
     */
    public function getPath() {
        return $this->_origin;
    }

    /**
     * Get the file name or dir basename
     * 
     * @return string
     */
    public function getName() {
        if (is_dir($this->_origin)) {
            return pathinfo($this->_origin, PATHINFO_BASENAME);
        }
        return pathinfo($this->_origin, PATHINFO_FILENAME);
    }

    /**
     * Get the file extension (empty for dir)
     * 
     * @return string
     */
    public function getType() {
        if (is_dir($this->_origin)) {
            return '';
        }
        return pathinfo($this->_origin, PATHINFO_EXTENSION);
    }

    public function getMime() {
        if (is_dir($this->_origin)) {
            return '';
        }
        $finfo = new \finfo(FILEINFO_MIME);
        return $finfo->file($this->_origin, FILEINFO_PRESERVE_ATIME);
    }

    /**
     * Get file content as array
     * 
     * @param boolean $keep true for keep empty lines
     * @return array|boolean FALSE when error
     */
    public function get($keep = false) {
        if (is_dir($this->_origin)) {
            $cdir = scandir($this->_origin);
            return $keep ? $cdir : array_filter($cdir, function($val) {
                        return '.' !== $val[0];
                    });
        } else {
            if ($content = str_replace("\r", '', $this->getContent())) {
                $array = explode("\n", $content);
            }
            return $keep ? $array : array_filter($array);
        }
        return FALSE;
    }

    /**
     * Get file content (binary)
     * 
     * @return boolean
     */
    public function getContent() {
        if (!is_dir($this->_origin)) {
            return $this->_content? : $this->_content = file_get_contents($this->_origin);
        }
        return FALSE;
    }

    public function getLength() {
        if (!is_dir($this->_origin)) {
            return filesize($this->_origin);
        }
        return 0;
    }

    /**
     * Write/Append $data to file
     * 
     * @param string|array|Resource $data
     * @param int $mode 0|FILE_APPEND
     */
    private function _write($data, $mode) {
        if ($data instanceof \Wtf\Interfaces\Readable) {
            $res = file_put_contents($this->_origin, $data->getContent(), $mode);
        } elseif (is_array($data)) {
            $res = file_put_contents($this->_origin, implode(PHP_EOL, $data), $mode);
        } else {
            $res = file_put_contents($this->_origin, $data, $mode);
        }
        if (FALSE === $res) {
            trigger_error(__CLASS__ . "::write: error in file '{$this->_origin}'");
        }
        return $this;
    }

    /**
     * Append $data to file
     * 
     * @param string|array|Resource $data
     */
    public function append($data) {
        return $this->_write($data, FILE_APPEND);
    }

    /**
     * Write/overwrite $data to file
     * 
     * @param string|array|Resource $data
     */
    public function put($data) {
        return $this->_write($data, 0);
    }

    /**
     * Remove file | directory
     */
    public function remove() {
        if (is_dir($this->_origin)) {
            return static::_delTree($this->_origin);
        }
        return unlink($this->_origin);
    }

    /**
     * Recursive remove dir tree
     * 
     * @param type $dir
     * @return type
     */
    private static function _delTree($dir) {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? static::_delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

}
