<?php
namespace talnet;

/**
 * Class Talnet
 * @package talnet
 *
 * Useless class. Only exists to ensure backwards compatibility with Talnet PHP Lib v1.0
 */
class Talnet {
    private static $_app;

    /**
     * Sets the current app used by the library. Must be called before any other function.
     *
     * @param App $app App object
     */
    public static function setApp($app) {
        Talnet::$_app = new Application($app->APP_NAME, $app->APP_KEY);
    }

    public static function getApp() {
        return Talnet::$_app;
    }
	
	/**
	 * Returns the year number for the current first year in the program.
	 * Assumes years start on September 1st.
	 */
	public static function getFirstYear() {
		return Utilities::getFirstYear();
	}
	
	/**
	 * Returns the phone number prefixes supported by Talnet in an array.
	 */
	public static function getPhonePrefixes() {
		return Utilities::getPhonePrefixes();
	}

    static function convertNumberToHebrew($num)
    {
        return Utilities::convertNumberToHebrew($num);
    }

}
