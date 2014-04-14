<?php
/**
 * Created by PhpStorm.
 * User: Guy Weizman
 * Date: 26/03/14
 * Time: 16:18
 */

namespace talnet;
use Exception;


require_once ("BaseCondition.php");


class Condition {
    // Recursively built
    private $_type; // OR, AND, Node
    private $_left, $_right; // Only $_left is used in case of "Node"

    function __construct ($left, $right = NULL, $type = "Node") {
        $this->_left = $left;
        $this->_right = $right;
        $this->_type = $type;
    }

    /**
     * Method returning a JSON form of the condition
     *
     * @return string
     */
    public function JSON() {
        if ($this->_type == "Node")
        {
            return $this->_left->JSON();
        }
        else if ($this->_type == "OR" || $this->_type == "AND")
        {
            return array (
              $this->_type => array (
                  "firstStatement" => $this->_left->JSON(),
                  "secondStatement" => $this->_right->JSON()
              )
            );
        }
        else
        {
            throw new Exception("Unknown type");
        }
    }
}
