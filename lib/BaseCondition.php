<?php
/**
 * Created by PhpStorm.
 * User: Ido Bronstein and Omri Lifshitz
 * Date: 26/03/14
 * Time: 16:18
 */

namespace talnet;

class BaseCondition
{
    private $_field; //The field in which to check the condition
    private $_op; //Operator of the condition
    private $_value; //Value of the condition

    /**
     * Constructor for a basic condition only containing a field, operator and a value
     * @param $field - The field which we are checking
     * @param $op - operator
     * @param $value - value
     */
    function __construct($field, $op, $value)
    {
        $this->_field = $field;
        $this->_op=$op;
        $this->_value=$value;
    }

    /**
     * Method returning a JSON form of the condition
     *
     * @return string
     */
    public function JSON()
    {
        return array(
            "Field" => $this->_field,
            "Op" => $this->_op,
            "Value"  => $this->_value
        );
    }
}

