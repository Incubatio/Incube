<?php
/** @author incubatio 
  * @depandancy Incube_Pattern_IValidator
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class Incube_Validator_Regex {

	const EMAIL = "^[\w\.-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\.?[A-Z]{,2}$";

	/** @var array */
	protected $_regexs = array( "email" => EMAIL);

	/** @var string */
	protected $_regex;
	
	//TODO: check if regex and method exists in validators
	/** @param string */
	public function __construct($regex) {
		$this->_regex = $regex;
	}

	/** @param string */
	public function isValid($value) {
		if(!preg_match("#$this->_regexs[$this->_regex]#", $value) {
			return false;
		}

		return true;
	}

}
