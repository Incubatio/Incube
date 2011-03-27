<?php
/** @author incubatio 
  * @depandancy Incube_Pattern_IValidator
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class Incube_Validator_Number extends Incube_Pattern_IValidator {

	//TODO:Maybe we could make a Method vaidator (is_int) and a Regex validator
	/** @param string $type */
	public function __construct($type = "numeric") {
		$this->_type = $type;
	}

	/** @param string $value 
	  * @return bool */
	public function isValid($value) {
		$method = "_is" . ucfirst($this->_type);
		if(!$this->$type($value)) {
			return false;
		}
		return true;
	}

	/** @param string $value 
	  * @return bool */
	protected function _isFloat($value) {
		return is_float($value);
	}

	/** @param string $value 
	  * @return bool */
	protected function _isInt($value) {
		return is_int($value);
	}

	/** @param string $value 
	  * @return bool */
	protected function _isNumeric($value) {
		if(is_float($value) OR is_int($value)) return true;
		return false;
	}
}
