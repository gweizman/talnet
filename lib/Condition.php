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
    private $_cond= "";// The condition in the given JSON format

    function __construct ($left, $right = NULL, $type = "Node") {
        $_left = $left;
        $_right = $right;
        $_type = $type;
    }

    //A method that parses a given condition into Field, operator and value
    private function parser($left)
    {

    }

    public function JSON() {
        if ($this->_type == "Node")
        {
            $this->_cond= "TERM: {" + $this->parser($this->_left) + "}";
            return $this->_cond;
        }
        else if ($this->_type == "OR")
        {

            $this->_cond= "OR: {firstStatement: {" + $this->_left.JSON() + "}, secondStatement: {" + $this->_right.JSON() + "}";
            return $this->_cond;
        }
        else if ($this->_type == "AND")
        {
            $this->_cond= "OR: {firstStatement: {" + $this->_left.JSON() + "}, secondStatement: {" + $this->_right.JSON() + "}";
            return $this->_cond;
        }
    }
} 