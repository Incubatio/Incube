<?php
/** @author incubatio
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  * 
  * SingleTime is a singleton where you can get only one time it instance
  * to avoid spaghertti usage of singleton.
  *
  */

abstract class Incube_Pattern_SingleTime_Abstract {

	protected static $_used = false;

	/** @return Oject */
	public static function getInstance() {
		if(!self::$_used) {
			self::$_used = true;
			//php 5.3 needed to use get called class, copy this method in the child class
			//$c = get_called_class();
			$c = __CLASS__;
			return new $c;
		}
        trigger_error("You can get the instance of $c only one time.", E_USER_ERROR);
	}

    public final function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

	abstract protected function __construct();
}
