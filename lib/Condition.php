<?php
/**
 * Created by PhpStorm.
 * User: Guy Weizman
 * Date: 26/03/14
 * Time: 16:18
 */

namespace U443;


class Condition {
    // Recursively built
    private $_type; // OR, AND, Node
    private $_left, $_right; // Only $_left is used in case of "Node"

    function __construct ($left, $right = NULL, $type = "Node") {
        $_left = $left;
        $_right = $right;
        $_type = $type;
    }

    public function JSON() {
        if ($this->_type == "Node")
        {
            //Json??
            retun;
        }
        else if ($this->_type == "OR")
        {
            //OR for Json??
            return;
        }
        else if ($this->_type == "AND")
        {
            //
        }
    }
} 