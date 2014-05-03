<?php
namespace talnet;

class Order
{
    private $_sortBy;
    private $_dir;

    public function __construct($sortBy, $direction = "ASC")
    {
        $this->_sortBy = $sortBy;
        $this->_dir = $direction;
    }

    public function JSON()
    {
        return array(
            "by" => $this->_sortBy,
            "dir" => $this->_dir
        );
    }
}