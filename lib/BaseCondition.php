<?php
/**
 * Created by PhpStorm.
 * User: Ido Bronstein and Omri Lifshitz
 * Date: 26/03/14
 * Time: 16:18
 */

namespace U443;

class BaseCondition
{
    private $_field; //The field in which to check the condition
    private $_op; //Operator of the condition
    private $_value; //Value of the condition

    function __constructor($field, $op, $value)
    {
        $_field=$field;
        $_op=$op;
        $_value=$value;
    }

    public function JSON()
    {
        return "Field:" . $this->_field . ", Op:" . $this->_op +", Value:" . $this->_value;
    }
}

