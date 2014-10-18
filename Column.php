<?php
namespace talnet;

// Basically a wrapper
class Column
{
    private $_name, $_type, $_size, $_primary, $_autoinc;

    public function __construct($name, $type, $size = NULL, $primary = NULL, $autoinc = NULL)
    {
        $this->_name = $name;
        $this->_type = $type;
        $this->_size = $size;
        $this->_primary = $primary;
        $this->_autoinc = $autoinc;
    }

    public function JSON()
    {
        $json = array(
            "colName" => $this->_name,
            "colType" => $this->_type
        );
        if ($this->_size != NULL) {
            $json['size'] = $this->_size;
        }
        if ($this->_primary != NULL) {
            $json['isPrimary'] = $this->_primary;
        }
        if ($this->_autoinc != NULL) {
            $json['autoInc'] = $this->_autoinc;
        }

        return $json;
    }
} 
