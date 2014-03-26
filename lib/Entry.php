<?php
/**
 * Created by PhpStorm.
 * User: Guy Weizman
 * Date: 26/03/14
 * Time: 16:17
 */

namespace U43;


class Entry {
    private $_app, $_table, $_columns; // Columns is a dictionary of name : type

    public function __constructor ($keys) {
        // $keys is a dictionary of name : value
        // Returns a Entry object of the created row
        // Performs INSERT using U43::send()
    }

    public function __set($name, $value) {
        // Generic setter, must check input validity
        // Performs update if needed
    }

    public function __get($name) {
        // Generic getter, must check input validity
        // All values should already be set locally, no SQL needed
    }

    public function remove() {
        // Removes entry from table
    }

    public static function get($condition) {
        // Returns a list of Entry objects satisfying $condition
    }
} 