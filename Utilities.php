<?php
namespace talnet;

class Utilities {
    public static function generalSelect($tableNames, $condition = NULL, $order = NULL, $app = null){
        if ($app == null)
            $app = Talnet::getApp();
        $tables = [];
        foreach($tableNames as $tableName){
            array_push($tables, array('tableName' => $tableName));
        }
        $request = RequestFactory::createDtdAction($tables, "SELECT", NULL, $condition, $order);
        $records = $app->send($request);
        return $records;
    }

    public function generalSet($toApp, $table, $field, $val, $name, $value, $app = null)
    {
        if ($app == null)
            $app = Talnet::getApp();
        $condition = new BaseCondition($field, "=", $val);
        $data = array($name => $value);
        $request = RequestFactory::createDtdAction($table, "UPDATE", $data, $condition, $toApp);
        return $app->send($request);
    }
    
    public static function generalCount($tableNames, $condition = NULL, $app = null){
        if ($app == null)
            $app = Talnet::getApp();
        $tables = [];
        foreach($tableNames as $tableName){
            array_push($tables, array('tableName' => $tableName));
        }
        $request = RequestFactory::createDtdAction($tables, "COUNT", NULL, $condition);
        $records = $app->send($request);
        return $records[0]->resultLength;
    }
    
    public static function challenge($field, $challenge)
    {
        return Utilities::encrypt($field . trim($challenge));
    }

    public static function encrypt($text)
    {
        return md5($text);
    }

    /**
     * Returns the year number for the current first year in the program.
     * Assumes years start on September 1st.
     */
    public static function getFirstYear() {
        return date_diff(date_create('1978-09-01'), date_create('today'))->y;
    }

    /**
     * Returns the phone number prefixes supported by Talnet in an array.
     */
    public static function getPhonePrefixes() {
        return array( '050', '051', '052', '053', '054', '055', '057', '058' );
    }

    static function convertNumberToHebrew($num)
    {
        mb_language('uni');
        mb_internal_encoding('UTF-8');
        $text = $num;
        $daf = '';
        // split off daf
        $lastChar = strtolower($text{strlen($text) - 1});
        if ($lastChar == 'a' or $lastChar == 'b') {
            $daf = $lastChar;
        }

        $number = intval($text);
        $output = "";

        // Do thousands
        $thousands = Utilities::calcOnes(intval($number / 1000) % 10);
        if ($thousands != null)
            $output .= $thousands . "'";

        // Do hundreds
        $hundreds = Utilities::calcHundreds(intval($number / 100) % 10);
        $output .= $hundreds;

        // fix exceptions
        if ($number % 100 == 15) {
            $ones = Utilities::calcOnes(9);
            $output .= $ones;
            $ones = Utilities::calcOnes(6);
            $output .= $ones;
        } elseif ($number % 100 == 16) {
            $ones = Utilities::calcOnes(9);
            $output .= $ones;
            $ones = Utilities::calcOnes(7);
            $output .= $ones;
        } else {
            // Do tens
            $tens = Utilities::calcTens(intval($number / 10) % 10);
            $output .= $tens;

            // Do ones
            $ones = Utilities::calcOnes(intval($number) % 10);
            $output .= $ones;
        }
        // Add dot or apostrophe
        if ($daf == 'a') {
            $output .= '.'; // This is really the wrong symbol.
        } elseif ($daf == 'b') {
            $output .= Utilities::unichr(hexdec("5C3"));
        } elseif (mb_strlen($output) > 1) {
            $output = mb_substr($output, 0, -1) . '"' . mb_substr($output, -1, mb_strlen($output) + 1);
        } elseif (mb_strlen($output) == 1) {
            $output .= "'";
        }

        return $output;
    }

    private static function calcHundreds($digit)
    {
        if ($digit != null) {
            $output = "";
            while ($digit >= 4) {
                $output .= Utilities::unichr(hexdec("5EA"));
                $digit -= 4;
            }
            // add number to numerical value for unicode character before "kuf"
            if ($digit > 0)
                $output .= Utilities::unichr($digit + 1510);

            return $output;
        }
    }

    private static function calcTens($digit)
    {
        if ($digit != null) {
            // store the unicode value in decimal of hebrew representation for tens
            $tensUnicodes = array("5D9", "5DB", "5DC", "5DE", "5E0", "5E1", "5E2", "5E4", "5E6");
            return Utilities::unichr(hexdec($tensUnicodes[$digit - 1]));
        }
    }

    private static function calcOnes($digit)
    {
        if ($digit != null)
            // add number to numerical value for unicode character before "aleph"
            return Utilities::unichr($digit + 1487);
    }

    // returns a one character string containing the unicode character specified by unicode
    // from http://www.php.net/manual/en/function.chr.php#88611
    static function unichr($unicode)
    {
        return mb_convert_encoding('&#' . $unicode . ';', 'UTF-8', 'HTML-ENTITIES');
    }
    
	/**
	 * Extract an array of the values of a specific field in an enumerable group of objects.
	 *
	 * @param objects - enumerable group of objects
	 * @param field - a field name present in all the objects
	 * @return an array of the values of the field in the objects
	 */
	static public function fieldArray($objects, $field, $index = NULL) {
		$arr = array();
		
		foreach ($objects as $object) {
			if($index == NULL) {
				array_push($arr, $object->$field);
			} else {
				if($field == ''){
					$arr[$object->$index] = $object;
				} else {
					$arr[$object->$index] = $object->$field;
				}
			}
		}
		
		return $arr;
	}

}
?>
