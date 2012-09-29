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
 * @author      Jeroen Van Meerendonk
 * @author      Joseba Juániz
 * @copyright   Copyright (c) Eric Barnes. (http://ericlbarnes.com/)
 * @copyright   Copyright (c) Cyneek. (http://cyneek.com)
 * @license     http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link        http://cyneek.com
 * @since       Version 1.2
 * @filesource
 *
 */

// ------------------------------------------------------------------------

/**
 * Minify CSS Driver
 *
 * @subpackage  Drivers
 */
class Compactor_css {

    /**
     * Constructor
     *
     * @return \Minify_css
     */
    public function __construct() {
        Log::write('debug', 'Minify CSS Initialized');
    }

    // ------------------------------------------------------------------------

    /**
     * Min
     *
     * Minify a CSS file
     *
     * @param string $file
     * @param bool $compact
     * @param null $is_aggregated
     * @return string
     */
    public function min($file, $compact = TRUE, $is_aggregated = NULL) {
        if (is_file($file)) {
            $file = file_get_contents($file);
        }

        if ($is_aggregated) {
            $file = $this -> remove_charsets($file);
        }

        if ($compact != FALSE) {
            return trim($this -> _optimize($file)) . "\n";
        } else {
            return "\n" . trim($file) . "\n\n";
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Remove charsets
     *
     * Charset declarations removal to support do combine function
     * in order to set a new one user defined charset at the beggining of the document
     * to keep standars compliance (and fix Webkit buggy behaviours)
     *
     * @param string $contents
     * @return string
     */
    private function remove_charsets($contents) {
        return preg_replace('/^\s*@charset\s+[\'"](?:\S*)\b[\'"];/i', '', $contents);
    }

    // ------------------------------------------------------------------------

    /**
     * Optimize
     * Optimize the contents of a css file
     * based on Drupal 7 CSS Core aggregator
     *
     * @param string $contents
     * @return string
     */
    private function _optimize($contents) {
        // Perform some safe CSS optimizations.
        // Regexp to match comment blocks.
        $comment = '/\*[^*]*\*+(?:[^/*][^*]*\*+)*/';
        // Regexp to match double quoted strings.
        $double_quot = '"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"';
        // Regexp to match single quoted strings.
        $single_quot = "'[^'\\\\]*(?:\\\\.[^'\\\\]*)*'";
        // Strip all comment blocks, but keep double/single quoted strings.
        $contents = preg_replace("<($double_quot|$single_quot)|$comment>Ss", "$1", $contents);
        // Remove certain whitespace.
        // There are different conditions for removing leading and trailing
        // whitespace.
        // @see http://php.net/manual/en/regexp.reference.subpatterns.php
        $contents = preg_replace_callback('<' .
        # Strip leading and trailing whitespace.
        '\s*([@{};,])\s*' .
        # Strip only leading whitespace from:
        # - Closing parenthesis: Retain "@media (bar) and foo".
        '| \s+([\)])' .
        # Strip only trailing whitespace from:
        # - Opening parenthesis: Retain "@media (bar) and foo".
        # - Colon: Retain :pseudo-selectors.
        '| ([\(:])\s+' . '>xS', array(get_class($this), '_optimize_call_back'), $contents);

        return $contents;
    }

    // ------------------------------------------------------------------------

    /**
     * Optimize CB
     * Optimize Callback Helper companion for optimize fn
     * based on Drupal 7 CSS Core aggregator
     *
     * @param string $matches
     * @return array
     */
    private function _optimize_call_back($matches) {
        // Discard the full match.
        unset($matches[0]);

        // Use the non-empty match.
        return current(array_filter($matches));
    }

}

/* End of file Compactor_css.php */
/* Location: ./bundles/compactor/libraries/compactor.php */
