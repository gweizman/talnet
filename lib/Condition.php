<?php
/**
 * Created by PhpStorm.
 * User: Guy Weizman
 * Date: 26/03/14
 * Time: 16:18
 */

namespace U43;


class Condition {
    // Recursively built
    private $_type; // OR, AND, Node
    private $_left, $_right; // Only $_left is used in case of "Node"

    function __construct ($left, $right = NULL, $type = "Node") {

    }

    function JSON() {
        // Recursive
    }
} 