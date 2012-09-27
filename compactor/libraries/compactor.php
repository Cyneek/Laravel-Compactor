<?php
/**
 * Laravel Compactor
 *
 * A minification bundle for Laravel
 *
 * Based on the Minify Driver for Codeigniter by Eric Barnes
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Open Software License version 3.0
 *
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt / license.rst.  It is
 * also available through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 *
 * @package     Laravel-Compactor
 * @author      Eric Barnes
 * @author      Jeroen Van M.
 * @author      Joseba J.
 * @copyright   Copyright (c) Eric Barnes. (http://ericlbarnes.com/)
 * @copyright   Copyright (c) Cyneek. (http://cyneek.com)
 * @license     http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link        http://cyneek.com
 * @since       Version 1.0
 * @filesource
 *
 */

// ------------------------------------------------------------------------

use FilesystemIterator as fIterator;

class Compactor {
    /**
     * Construct
     *
     * Initialize params
     *
     * @return \Minify
     */
     
     
    var $contents = NULL;
     
    public function __construct() {
        Log::write('debug', 'Compactor: Library nitialized.');
    }

    /**
     * overwrites the Get method of the driver library from Codeigniter
     * it uses lazy loading of the css and js drivers
     *
     * @return  void
     * @param
     */
    function __get($child) {

        if (!isset($this -> $child)) {
            $child_file = __DIR__ . '/drivers/' . strtolower(get_class($this)) . '_' . $child . '.php';
            $child_class = get_class($this) . '_' . $child;

            if (file_exists($child_file)) {
                include_once $child_file;
            } else {
                Log::write('error', 'Compactor->unable to load the requested file: ' . $child_file);
            }

            if (!class_exists($child_class)) {
                Log::write('error', 'Compactor->unable to load the requested driver: ' . $child_class);
            }

            $obj = new $child_class;
            $this -> $child = $obj;
            return $this -> $child;

        }

        return $this -> $child;
    }

    // ------------------------------------------------------------------------

    /**
     * Combine Files
     *
     * Pass an array of files and combine them.
     * @param array $files
     * @param string $type
     * @param bool $compact
     * @param string $css_charset
     * @return mixed
     */
    public function combine_files($files = array(), $type = '', $compact = TRUE, $css_charset = 'utf-8') {
        if (!is_array($files) OR count($files) < 1) {
            Log::write('error', 'Compactor->combine_files missing files array');
            return FALSE;
        }

        return $this -> _do_combine($files, $type, $compact, $css_charset);
    }

    // ------------------------------------------------------------------------

    /**
     * Combine Directory
     *
     * Pass a directory and combine all the files into one string.
     *
     * @param string $directory
     * @param array $ignore
     * @param string $type
     * @param bool $compact
     * @param string $css_charset
     * @return string
     */
    public function combine_directory($directory = '', Array $ignore = array(), $type = '', $compact = TRUE, $css_charset = 'utf-8') {
        $available = array();

        if ($directory == '' OR !is_dir($directory)) {
            Log::write('error', 'Compactor->combine_directory missing files array');
            return FALSE;
        }

        $items = new fIterator($directory, fIterator::SKIP_DOTS);

        //we get all the files with a file iterator
        foreach ($items as $dir => $file) {

            if (File::mime($dir) == 'application/octet-stream' and ($this->_get_type($dir) == 'js' OR $this->_get_type($dir) == 'css')) {
                $available[$file -> getBaseName()] = $dir;
            }
        }

        // Finally unset the ignored files
        if (count($ignore) > 0) {
            foreach ($available AS $key => $file) {
                if (in_array($key, $ignore)) {
                    unset($available[$key]);
                }
            }
        }

        return $this -> _do_combine($available, $type, $compact, $css_charset);
    }

    // ------------------------------------------------------------------------

    /**
     * Do combine
     *
     * Combine all the files and return a string.
     *
     * @param array $files
     * @param string $type
     * @param bool $compact
     * @param string $css_charset
     * @return string
     */
      private function _do_combine($files, $type, $compact = TRUE, $css_charset = 'utf-8') {
            
        $this->contents = NULL;
        $contents = '';
        $file_count = 0;

        foreach ($files AS $file) {
            if (!file_exists($file)) {
                Log::write('error', 'Compactor->_do_combine missing file ' . $file);
                continue;
            }

            $file_count++;

            if ($type == '') {
                $type = $this -> _get_type($file);
            }

            $path_info = pathinfo($file, PATHINFO_BASENAME);
            // Referal File and path

            if ($type == 'css') {
                // only one charset placed at the beginning of the document is allowed
                // in order to keep standars compliance and fixing Webkit problems
                // Note: Minify_css driver yet remove all charsets previously
                if ($file_count == 1) {
                    $contents .= '@charset "' . $css_charset . '";' . "\n";
                }
                $contents .= "\n" . '/* @fileRef ' . $path_info . ' */' . "\n";
                $contents .= $this -> css -> min($file, $compact, $is_aggregated = TRUE);
            } elseif ($type == 'js') {
                unset($css_charset);
                $contents .= "\n" . '// @fileRef ' . $path_info . ' ' . "\n";
                $contents .= $this -> js -> min($file, $compact);
            } else {
                $contents .= $file . "\n\n";
            }
        }
            
        $this->contents = $contents;
        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Save File
     *
     * Save a file with the compacted contents
     *
     * @param string $full_path
     * @return bool
     */
     public function save_file($full_path = '') {
        if (!File::put($full_path, $this->show_contents())) {
            Log::write('error', 'Compactor->save_file could not write file');
            return FALSE;
        }
        return TRUE;
    }

    /**
     *  in case the user don't call show_contents when echoeing, it will be called in the
     *  toString function 
     *
     * @return String
     */
    public function __toString()
    {
        return $this->show_contents();
    }


    /**
     *  return the contents of the compacted files
     *
     * @return String
     */
    public function show_contents()
    {
        $return_data = $this->contents;
        $this->contents = NULL;
        return $return_data;
    }

    // ------------------------------------------------------------------------

    /**
     * Get Type
     *
     * Get the file extension to determine file type
     *
     * @param string $file
     * @return string
     */
    private function _get_type($file) {
        return File::extension($file);
    }

}

/* End of file compactor.php */
/* Location: ./bundles/compactor/libraries/compactor.php */