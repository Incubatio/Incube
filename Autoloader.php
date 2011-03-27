<?php
/** @author incubatio
  * @depandancy Incube_Pattern_SingleTime_Abstract
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
require_once "Incube/Pattern/SingleTime/Abstract.php";
class Incube_Autoloader extends Incube_Pattern_SingleTime_Abstract {

	/** @var bool */
	protected static $_used = false;

	/** @return Incube_Autoloader */
	public static function getInstance() {
		if(!self::$_used) {
			self::$_used = true;
			//PHP5.3 needed to use get called class, copy this method in the child class
			//$c = get_called_class();
			$c = __CLASS__;
			return new $c;
		}
        trigger_error("You can get the instance of $c only one time.", E_USER_ERROR);
	}

	protected function __construct() {
        spl_autoload_register(array(__CLASS__, 'loadClass'));
	}

	/** Load a class from it Name replacing the separator "_" by the directory separator "/"
	  * Example: My_Class should be available in one of your (get_)include_path + My/Class.php
	  *
	  * @param string $class
      * @return string */
	public static function loadClass($class) {
		$path = preg_replace("#_#", DIRECTORY_SEPARATOR, $class) . ".php";
		include_once($path);
		return $class;
	}

}
